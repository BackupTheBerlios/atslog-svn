/*

	(C) Alexey V. Kuznetsov, avk@gamma.ru, 2001, 2002

*/

#include <ctype.h>
#include <stdio.h>
#include <stdarg.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <errno.h>
#include <fcntl.h>

#include <signal.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#ifndef WIN32
#include <termios.h>
#include <sysexits.h>
#endif

#ifndef GETOPT
#include <unistd.h>
#endif

#ifdef WIN32
#include <windows.h>
#else
typedef long HANDLE;
typedef long DWORD;
#define INVALID_HANDLE_VALUE (HANDLE)(-1)
#endif

#define	MAXSTRINGLEN	1028
#define MAXPATHLEN	256
#define MAXFILENAMELEN	256
#define MAXERRORLEN	256

#define	SPACE_TO_ZERO(x)	((x)==' ' ? '0' : (x))

HANDLE h2close=INVALID_HANDLE_VALUE;
struct in_addr remote_end={0};

char dbg=0;
char copy_to_stderr=0;
char copy_to_stdout=0;
char by_month=0;
char swap_day_month=0;
FILE *errout;
char dirname[MAXPATHLEN+1+MAXFILENAMELEN+1];
int dirlen=0;

#define LT_RAW		0
#define LT_DEFINITY	1
#define LT_MERLIN	2
#define LT_PANASONIC	3
#define LT_MERIDIAN	4
#define LT_GHX		5
#define LT_MD110	6

struct LogType {
	char *name;
	int type;
} logtypes[] = {
	{ "raw",		LT_RAW },
	{ "definity",		LT_DEFINITY },
	{ "merlin",		LT_MERLIN },
	{ "panasonic",		LT_PANASONIC },
	{ "meridian",		LT_MERIDIAN },
	{ "ghx",		LT_GHX },
	{ "md110",		LT_MD110 },
	{ NULL, 0 }
};

struct String {
	char		*s;
	struct String	*next;
};

int logtype=LT_RAW;
FILE *cur_logfile=NULL;
int cur_month=0,new_month=0;
int cur_day=0,new_day=0;
struct String *cdrs=NULL;
struct String *cdrs_last=NULL;

#ifdef GETOPT

char *optarg=NULL;
int optind=1;

static int getopt( int argc,char *argv[],char *sw )
{
	char *s,sym;
	if( optind>=argc ) return (-1);
	if( argv[optind][0]!='-' && argv[optind][0]!='/' ) return (-1);
	sym=argv[optind][1];
	s=strchr( sw,sym );
	optind++;
	if( s==NULL || *s==0 ) {
		return sym;
	}
	if( s[1]==':' ) {
		if( optind>=argc ) {
			optarg=NULL;
		} else {
			optarg=argv[optind];
			optind++;
		}
	}
	return sym;
}

#else

extern char *optarg;
extern int optind;

#endif

#ifndef WIN32

static int daemonize( void )
{
	int rc;
	rc = fork();
	if( rc==(-1) ) return (-1);
	if( rc!=0 ) _exit(EX_OK);
	if( setsid()==(-1) ) return (-1);
	return 0;
}

#endif


int my_syslog( char *s, ... )
{
	va_list va;
	time_t tm=time(NULL);
	char *t;
	int l=strlen(s)-1;

	va_start( va,s );
	t=ctime(&tm); l=strlen(t)-1;
	if( t[l]=='\n' ) t[l]=0;
	fprintf( errout, "%s: ",t );
	if( copy_to_stderr ) fprintf( stderr, "%s: ",t );
	l=strlen(s)-1;
	if( s[l]=='\n' ) s[l]=0;
	vfprintf( errout,s,va );
	fprintf( errout,"\n" );
	if( copy_to_stderr ) {
		vfprintf( stderr,s,va );
		fprintf( stderr,"\n" );
	}
	fflush( errout );
	if( copy_to_stderr ) fflush( stderr );
	return 0;
}

static void *my_malloc( int size,char do_clr )
{
	void *p=malloc(size);
	if( p==NULL ) {
		my_syslog( "can't allocate %d bytes: %s",strerror(errno) );
		exit( 1 );
	}
	if( do_clr ) memset( p,0,size );
	return p;
}

static char *my_strdup( char *s )
{
	int l=strlen(s)+1;
	char *p;
	p=my_malloc( l,0 );
	memcpy( p,s,l );
	return p;
}

static void my_fputs( char *s,FILE *fp )
{
	fputs( s,fp );
	if( copy_to_stdout ) {
		fputs( s,stdout );
	}
}

static void my_fflush( FILE *fp )
{
	fflush( fp );
	if( copy_to_stdout ) {
		fflush( stdout );
	}
}

static void sighandler(int sig)
{
	if( h2close!=INVALID_HANDLE_VALUE ) {
		my_syslog( "closing CDR stream" );
		close( h2close );
	}
	my_syslog( "exiting on signal %d",sig );
	exit( 0 );
}

static void setsighandler(int sig )
{
	struct sigaction sa;
	sigset_t ss;
	sigemptyset( &ss );
	sigaddset( &ss,sig );
	if( sigprocmask( SIG_UNBLOCK,&ss,NULL )==(-1) ) {
		my_syslog( "can't unblock signal %d: %s",sig,strerror(errno) );
		exit( 1 );
	}
	memset( &sa,0,sizeof(sa) );
	sa.sa_handler=sighandler;
	if( sigaction( sig,&sa,NULL )==(-1) ) {
		my_syslog( "can't set signal handler on %d: %s",sig,strerror(errno) );
		exit( 1 );
	}
}

static void put_cdr_to_q( char *s )
{
	struct String *ss=my_malloc( sizeof(struct String),1 );
	ss->s=my_strdup(s);
	if( cdrs==NULL ) cdrs=ss;
	if( cdrs_last!=NULL ) {
		cdrs_last->next=ss;
	}
	cdrs_last=ss;
}

static void flush_cdr_q( void )
{
	int i;
	struct String *ss;
	for( ss=cdrs,i=0; ss!=NULL; ss=ss->next,i++ ) {
		my_fputs( ss->s,cur_logfile );
		my_fputs( "\n",cur_logfile );
	}
	if( i ) {
		my_fflush( cur_logfile );
		my_syslog( "%d CDR records were stored before getting date",i );
	}
}

static void free_cdr_q( void )
{
	struct String *ss,*ss_next;
	for( ss=cdrs; ss!=NULL; ) {
		ss_next=ss->next;
		free( ss->s );
		free( ss );
		ss=ss_next;
	}
	cdrs=cdrs_last=NULL;
}

char *my_strerror(void)
{
	static char ret_err[MAXERRORLEN+1];
	int rc;
	int l;
#ifdef WIN32
	sprintf( ret_err,"[0x%08lX] ",GetLastError() );
#else
	sprintf( ret_err,"(%d) ",errno );
#endif
	l=strlen(ret_err);
#ifdef WIN32
	rc = FormatMessage(
		FORMAT_MESSAGE_FROM_SYSTEM | FORMAT_MESSAGE_IGNORE_INSERTS,
		NULL,
		GetLastError(),
		0,
		(LPTSTR) (ret_err+l),
		MAXERRORLEN-l,
		NULL 
	);
#else
	strncpy( ret_err+l,strerror(errno),MAXERRORLEN-l );
	ret_err[MAXERRORLEN]=0;
	rc=strlen(ret_err+l);
#endif
	if( rc==0 ) {
		ret_err[l-1]=0;
	} else {
		if( ret_err[l+rc-1]=='\n' ) ret_err[l+rc-1]=0;
	}
	return ret_err;
}

void open_cur_logfile( char do_flush_q )
{
	if( cur_logfile!=NULL ) {
		fclose( cur_logfile );
		cur_logfile=NULL;
	}
	if( by_month ) {
		sprintf( dirname+dirlen,"%02d.log",cur_month );
	} else {
		sprintf( dirname+dirlen,"%02d.%02d",cur_month,cur_day );
	}
	if( (cur_logfile=fopen(dirname,"at"))==NULL ) {
		my_syslog( "can't open CDR file '%s': %s",dirname,strerror(errno) );
		exit( 1 );
	}
	my_syslog( "CDR file '%s' opened",dirname );
	fputs( "\n",cur_logfile );
	if( do_flush_q ) {
		flush_cdr_q();
		free_cdr_q();
	}
}

void open_cur_logfile_with_check( char do_flush_q )
{
	if( swap_day_month ) {
		int i;
		i=new_day; new_day=new_month; new_month=i;
	}
	if( new_day==cur_day && new_month==cur_month ) return;
	cur_day=new_day; cur_month=new_month;
	open_cur_logfile( do_flush_q );
}

HANDLE open_socket( unsigned short port )
{
	HANDLE s,new_s;
	struct sockaddr_in sa_loc,sa_rem;
	socklen_t sa_rem_len;

	s=socket(PF_INET,SOCK_STREAM,0);
	if(s==INVALID_HANDLE_VALUE) {
		my_syslog( "socket() failed: %s",my_strerror() );
		return s;
	}
	h2close=s;
	memset( &sa_loc,0,sizeof(sa_loc) );
	memset( &sa_rem,0,sizeof(sa_loc) );
	sa_rem.sin_family=sa_loc.sin_family=AF_INET;
	sa_loc.sin_port=htons(port);
	if( bind(s,(struct sockaddr*)&sa_loc,sizeof(sa_loc) )==(-1) ) {
		my_syslog( "bind() on port %d failed: %s",port,my_strerror() );
		goto err_ret;
	}
	if( listen(s,1)==(-1) ) {
		my_syslog( "listen() failed: %s",my_strerror() );
		goto err_ret;
	}
	my_syslog( "waiting TCP connection on port %d",port );
	for( ;; ) {
		sa_rem_len=sizeof(sa_rem);
		if( (new_s=accept(s,(struct sockaddr*)&sa_rem,&sa_rem_len ))==(-1) ) {
			my_syslog( "accept() failed: %s",my_strerror() );
			err_ret:
			h2close=INVALID_HANDLE_VALUE;
			close(s);
			return INVALID_HANDLE_VALUE;
		}
		my_syslog( "connection from %s:%d",inet_ntoa(sa_rem.sin_addr),ntohs(sa_rem.sin_port) );
		if( remote_end.s_addr==0 ) break;
		if( remote_end.s_addr!=sa_rem.sin_addr.s_addr ) {
			close( new_s );
			my_syslog( "invalid remote IP address" );
			continue;
		} else {
			break;
		}
	}
	h2close=INVALID_HANDLE_VALUE;
	close(s);
	return new_s;
}

HANDLE open_tty( char *tty_name )
{
	HANDLE hCom;

#ifdef WIN32
	hCom = CreateFile( tty_name,
		GENERIC_READ | GENERIC_WRITE,
		0,    // comm devices must be opened w/exclusive-access
		NULL, // no security attributes
		OPEN_EXISTING, // comm devices must use OPEN_EXISTING
		0,    // not overlapped I/O
		NULL  // hTemplate must be NULL for comm devices
	);
#else
	hCom = open( tty_name,O_RDWR );
#endif

	if (hCom == INVALID_HANDLE_VALUE) {
		my_syslog( "open_tty on '%s' failed: %s",tty_name,my_strerror() );
	}
	return hCom;
}

int set_tty_params( HANDLE hCom,long speed,int data_bits,char parity,int stop_bits )
{
	static struct Speed {
		long	speed;
		DWORD	wspeed;
	} speeds[] = {
#ifdef WIN32
		{ 1200, CBR_1200 },
		{ 2400, CBR_2400 },
		{ 4800, CBR_4800 },
		{ 9600, CBR_9600 },
		{ 19200, CBR_19200 },
		{ 38400, CBR_38400 },
		{ 57600, CBR_57600 },
		{ 115200, CBR_115200 },
#else
		{ 1200, B1200 },
		{ 2400, B2400 },
		{ 4800, B4800 },
		{ 9600, B9600 },
		{ 19200, B19200 },
		{ 38400, B38400 },
		{ 57600, B57600 },
		{ 115200, B115200 },
#endif
		{ 0, 0 }
	};

	struct Speed *sp;

#ifdef WIN32
	DCB dcb;
	COMMTIMEOUTS cto;
	BOOL fSuccess;
#else
	struct termios tt;
#endif

	for( sp=speeds; sp->speed; sp++ ) {
		if( sp->speed==speed ) break;
	}
	if( sp->speed==0 ) {
		my_syslog( "Invalid speed: %ld",speed );
		return (-1);
	}
	if( stop_bits!=1 && stop_bits!=2 ) {
		my_syslog( "Invalid number of stop bits: %d",stop_bits );
		return (-1);
	}
	if( data_bits<5 || data_bits>8 ) {
		my_syslog( "Invalid number of data bits: %d",data_bits );
		return (-1);
	}

#ifdef WIN32
	memset( &dcb,0,sizeof(DCB) );
	dcb.DCBlength=sizeof(DCB);
	dcb.BaudRate=sp->wspeed;
	dcb.fBinary=TRUE;
	if( parity ) {
		dcb.fParity=TRUE;
		switch( parity ) {
		case 'e':
			dcb.Parity=EVENPARITY;
			break;
		case 'o':
			dcb.Parity=ODDPARITY;
			break;
		case 's':
			dcb.Parity=SPACEPARITY;
			break;
		case 'm':
			dcb.Parity=MARKPARITY;
			break;
		case 'n':
			dcb.Parity=NOPARITY;
			break;
		default:
			my_syslog( "Invalid parity: '%c'",parity );
			return (-1);
		}
	} else {
		dcb.fParity=FALSE;
	}
	dcb.fOutxCtsFlow=TRUE;
	dcb.fOutxDsrFlow=FALSE;
	dcb.fDtrControl=DTR_CONTROL_ENABLE;
	dcb.fDsrSensitivity=TRUE;
	dcb.fOutX=FALSE;
	dcb.fInX=FALSE;
	dcb.fErrorChar=FALSE;
	dcb.fNull=FALSE;
	dcb.fRtsControl=RTS_CONTROL_ENABLE;
	dcb.fAbortOnError=FALSE;
	dcb.ByteSize=data_bits;
	dcb.StopBits=stop_bits==2 ? TWOSTOPBITS : ONESTOPBIT;
	dcb.EofChar=0xFF;
	dcb.EvtChar=0xFF;

	fSuccess = SetCommState(hCom, &dcb);
	if (!fSuccess) {
		my_syslog( "SetCommState failed: %s", my_strerror() );
		return (-1);
	}

	cto.ReadIntervalTimeout=0;
	cto.ReadTotalTimeoutMultiplier=0;
	cto.ReadTotalTimeoutConstant=0;
	cto.WriteTotalTimeoutMultiplier=0;
	cto.WriteTotalTimeoutConstant=0;

	fSuccess = SetCommTimeouts(hCom, &cto);
	if (!fSuccess) {
		my_syslog( "SetCommTimeouts failed: %s", my_strerror());
		return (-1);
	}
#else
	cfmakeraw( &tt );
	tt.c_iflag = 0;
	tt.c_oflag = 0;
	tt.c_lflag = 0;
	tt.c_cflag = CREAD|CLOCAL|CRTSCTS|HUPCL;
	switch( data_bits ) {
	case 5:
		tt.c_cflag |= CS5;
		break;
	case 6:
		tt.c_cflag |= CS6;
		break;
       	case 7:
		tt.c_cflag |= CS7;
		break;
	default:
		tt.c_cflag |= CS8;
		break;
	}
	if( stop_bits==2 ) tt.c_cflag|=CSTOPB;
	switch( parity ) {
	case 'e':
		tt.c_cflag|=PARENB;
		break;
	case 'o':
		tt.c_cflag|=PARENB|PARODD;
		break;
	case 0  :
	case 'n':
		break;
	default:
		my_syslog( "Invalid parity: '%c'",parity );
		return (-1);
	}
	tt.c_cc[VTIME] = 0;
	tt.c_cc[VMIN] = 1;
	cfsetspeed( &tt,sp->wspeed );
	if( tcsetattr( hCom,TCSANOW,&tt )==(-1) ) {
		my_syslog( "tcsetattr failed: %s",my_strerror() );
		return (-1);
	}
#endif

	return 0;
}

int read_string( HANDLE hCom,char *buf,int blen )
{
	DWORD dwLength;
#ifdef WIN32
	BOOLEAN fSuccess;
#endif
	int count;

	for( count=0; count<blen; count++,buf++ ) {
		do {
#ifdef WIN32
			DWORD dwErrors;
			while( (fSuccess=ReadFile( hCom,buf,1,&dwLength,NULL )) ) {
#else
			while( (dwLength=read( hCom,buf,1 ))>=0 ) {
#endif
				if( dwLength==0 ) {
					if( dbg ) {
						my_syslog( "read returned zero length" );
					}
					buf[0]=0;
					if( dbg && count ) my_syslog( "read_string: '%s'",buf-count );
					return count;
				}
				if( *buf=='\n' || *buf=='\r' || *buf==0 ) {
					if( count ) {
						buf[0]=0;
						if( dbg ) my_syslog( "read_string: '%s'",buf-count );
						return count;
					} else {
						if( dbg ) my_syslog( "empty symbol '0x%02X'",(int)*buf );
						continue;
					}
				}
				break;
			}
#ifdef WIN32
			if( !fSuccess ) {
#else
			if( dwLength==(-1) ) {
#endif
#ifdef WIN32
				BOOLEAN fCceSuccess;
				fCceSuccess=ClearCommError( hCom,&dwErrors,NULL );
				if( !fCceSuccess ) {
					my_syslog( "ClearCommError failed: %s",my_strerror() );
				} else {
					my_syslog( "ClearCommError returned error 0x%lX",dwErrors );
				}
#else
				my_syslog( "read error: %s",my_strerror() );
#endif
			}
#ifdef WIN32
		} while( !fSuccess );
#else
		} while( dwLength<=0 );
#endif
	}
	return count;
}

void ParseClassicDate( char *dt )
{
	if( isdigit(SPACE_TO_ZERO(dt[0])) && isdigit(dt[1]) &&
		dt[2]=='/' &&
		isdigit(SPACE_TO_ZERO(dt[3])) && isdigit(dt[4]) &&
		dt[5]=='/' &&
		isdigit(SPACE_TO_ZERO(dt[6])) && isdigit(dt[7]) )
	{

		new_day=(SPACE_TO_ZERO(dt[3])-'0')*10+(dt[4]-'0');
		new_month=(SPACE_TO_ZERO(dt[0])-'0')*10+(dt[1]-'0');
		open_cur_logfile_with_check( 1 );
	}
}

void ParseMD110DateF2( char *dt )
{
	if( isdigit(dt[0]) && isdigit(dt[1]) &&
		isdigit(dt[2]) && isdigit(dt[3]) &&
		isdigit(dt[4]) && isdigit(dt[5]) &&
		isdigit(dt[6]) && isdigit(dt[7]) &&
		dt[8]==' ' )
	{

		new_day=(dt[2]-'0')*10+(dt[3]-'0');
		new_month=(dt[0]-'0')*10+(dt[1]-'0');
		open_cur_logfile_with_check( 1 );
	}
}

HANDLE open_io( char *io_name,long speed,int data_bits,char parity,int stop_bits )
{
	HANDLE hCom;
	int rc;
	unsigned short tcpPort=0;

	if( strncasecmp( io_name,"tcp:",4 )==0 ) {
		tcpPort=atoi(io_name+4);
		if( tcpPort==0 ) {
			my_syslog( "Invalid TCP port number" );
			return INVALID_HANDLE_VALUE;
		}
		hCom=open_socket(tcpPort);
		if (hCom == INVALID_HANDLE_VALUE) {
			my_syslog( "can't open '%s'",io_name );
			return INVALID_HANDLE_VALUE;
		}
	} else {
		hCom = open_tty( io_name );
		if (hCom == INVALID_HANDLE_VALUE) {
			my_syslog( "can't open serial device '%s'",io_name );
			return INVALID_HANDLE_VALUE;
		}
		rc = set_tty_params( hCom,speed,data_bits,parity,stop_bits );
		if( rc==(-1) ) {
			my_syslog( "Unable to set COM-port parameters" );
			return INVALID_HANDLE_VALUE;
		}
	}
	h2close=hCom;
	return hCom;
}

void usage( void )
{
	struct LogType *lt;

	fprintf( stderr,
"
CDR Reader for PBXs v.%s (C) Alexey V. Kuznetsov, avk@gamma.ru, 2001-2002
cdr_read [-D dir] [-L logfile] [-s speed] [-c csize] [-p parity] [-f sbits]
	 [-t type] [-d] [-e] [-a] [-o] dev
-D dir		directory where CDR files will be put, default is current dir
-L logfile	file for error messages, default is stderr
-s speed	speed of serial device, default 9600
-c char_size	length of character; valid values from 5 to 8
-p parity	parity of serial device:
		e - even parity, o - odd parity, n - no parity,
		m - mark parity (Win32 only), s - space parity (Win32 only)
-f stop_bits	number of stop bits; valid values 1 or 2
-d		output additional debug messages
-e		copy error messages to stderr (in case if -L has value)
-a		write date at the beginning of file (for Definity type only)
-o		write CDR additionally to stdout
-m		write log files on month-by-month instead of day-by-day basis
-n		consider day in place of month and vice versa
-r x.x.x.x	accept TCP connections from this IP address only
-w seconds	timeout before I/O port will be opened next time after EOF
-t cdr_type	type of CDR records, valid values (first is default)
",CDRR_VER);
	for( lt=logtypes; lt->name!=NULL; lt++ ) {
		fprintf( stderr,"\t%s",lt->name );
	}
#ifndef WIN32
	fprintf( stderr,
"-b		become daemon\n"
);
#endif
	exit( 1 );
}

int main( int argc, char *argv[] )
{
	int rc;
	char *logfile=NULL;
	long speed=9600;
	int data_bits=8,stop_bits=1;
	char parity=0;
	int next_open_timeout=5;
	char buf[MAXSTRINGLEN+1];
	struct LogType *lt;
	char write_date=0;

#ifndef WIN32
	char do_daemonize=0;
#endif
	sigset_t ss;

	HANDLE hCom;

#ifdef WIN32
	while( (rc=getopt(argc,argv,"oahdemnws:t:D:L:p:c:f:r:"))!=(-1) ) {
#else
	while( (rc=getopt(argc,argv,"boahdemnws:t:D:L:p:c:f:r:"))!=(-1) ) {
#endif
		switch( rc ) {
		case 'D':
			dirlen=strlen(optarg);
			if( dirlen>MAXPATHLEN ) {
				fprintf( stderr,"Too long directory name\n" );
				return 1;
			}
			if( dirlen==0 ) {
				dirname[0]='.'; dirlen=1;
			} else {
				memcpy( dirname,optarg,dirlen );
			}
			switch( dirname[dirlen-1] ) {
			case '/':
			case '\\':
				break;
			default:
				dirname[dirlen++]='/';
				break;
			}
			break;
		case 'L':
			logfile=optarg;
			break;
		case 's':
			speed=atol(optarg);
			break;
		case 'p':
			parity=optarg[0];
			break;
		case 'c':
			data_bits=atoi(optarg);
			break;
		case 'f':
			stop_bits=atoi(optarg);
			break;
		case 'd':
			dbg=1;
			break;
		case 'e':
			copy_to_stderr=1;
			break;
		case 'a':
			write_date=1;
			break;
		case 'r':
			if( inet_aton( optarg,&remote_end )==0 ) {
				fprintf( stderr,"Invalid IP address specified: %s\n",optarg );
				return 1;
			}
			break;
		case 'w':
			next_open_timeout=atoi(optarg);
			break;
#ifndef WIN32
		case 'b':
			do_daemonize=1;
			break;
#endif
		case 't':
			for( lt=logtypes; lt->name!=NULL; lt++ ) {
				if( strcmp( lt->name,optarg )==0 ) {
					logtype=lt->type;
					break;
				}
			}
			if( lt->name==NULL ) {
				fprintf( stderr,"Invalid CDR type: '%s'\n",optarg );
				return 1;
			}
			break;
		case 'o':
			copy_to_stdout=1;
			break;
		case 'm':
			by_month=1;
			break;
		case 'n':
			swap_day_month=1;
			break;
		case 'h':
			usage();
		default:
			fprintf( stderr,"Unknown switch: %c\n",(char)rc );
			exit( 1 );
		}
	}

	argc -= optind;
	argv += optind;

	if( logfile ) {
		errout=fopen( logfile,"at" );
		if( errout==NULL ) {
			fprintf( stderr,"Can't open '%s': %s\n",logfile,strerror(errno) );
			return 1;
		}
	} else {
		errout=stderr;
		copy_to_stderr=0;
	}

	if( argc<=0 ) {
		my_syslog( "No input COM-port given" );
		usage();
	}

	my_syslog( "starting" );

#ifndef WIN32
	if( do_daemonize && daemonize()==(-1) ) {
		my_syslog( "can't become daemon, exiting" );
		exit( 1 );
	}
#endif
	sigfillset( &ss );
	if( sigprocmask( SIG_SETMASK,&ss,NULL )==(-1) ) {
		my_syslog( "can't block all signals: %s",strerror( errno ) );
		exit( 1 );
	}
	setsighandler( SIGHUP );
	setsighandler( SIGINT );
	setsighandler( SIGTERM );

	hCom = open_io( argv[0],speed,data_bits,parity,stop_bits );
	if( hCom==INVALID_HANDLE_VALUE ) {
		my_syslog( "can't open '%s', exiting",argv[0] );
		return 1;
	}

	if( logtype==LT_RAW ) {
		memcpy( dirname+dirlen,"raw\0",4 );
		if( (cur_logfile=fopen(dirname,"at"))==NULL ) {
			my_syslog( "can't open CDR file '%s': %s",dirname,strerror(errno) );
			return 1;
		}
	}

	while( (rc=read_string( hCom,buf,MAXSTRINGLEN ))>=0 ) {
		char *dt;
		if( rc==0 ) {
			h2close=INVALID_HANDLE_VALUE;
			close( hCom );
			sleep( next_open_timeout );
			hCom = open_io( argv[0],speed,data_bits,parity,stop_bits );
			if( hCom==INVALID_HANDLE_VALUE ) {
				my_syslog( "can't open '%s', exiting",argv[0] );
				return 1;
			}
			continue;
		}
		switch( logtype ) {
		case LT_RAW:
			my_fputs( buf,cur_logfile );
			my_fputs( "\n",cur_logfile );
			my_fflush( cur_logfile );
			break;
		case LT_DEFINITY:
			if( rc==5 &&
				isdigit(buf[0]) && isdigit(buf[1]) &&
				buf[2]==' ' &&
				isdigit(buf[3]) && isdigit(buf[4]) )
			{
				new_day=(buf[0]-'0')*10+(buf[1]-'0');
				new_month=(buf[3]-'0')*10+(buf[4]-'0');
				new_log:
				if( swap_day_month ) {
					int i;
					i=new_day; new_day=new_month; new_month=i;
				}
				if( new_day!=cur_day || new_month!=cur_month ) {
					cur_day=new_day; cur_month=new_month;
					open_cur_logfile( 0 );
					if( write_date ) {
						my_fputs( buf,cur_logfile );
						my_fputs( "\n",cur_logfile );
						my_fflush( cur_logfile );

					}
					flush_cdr_q();
					free_cdr_q();
				}
				break;
			} else if( rc==11 &&
				isdigit(buf[0]) && isdigit(buf[1]) &&
				buf[2]==':' &&
				isdigit(buf[3]) && isdigit(buf[4]) &&
				buf[5]==' ' &&
				isdigit(buf[6]) && isdigit(buf[7]) &&
				buf[8]=='/' &&
				isdigit(buf[9]) && isdigit(buf[10]) )
			{
				new_day=(buf[6]-'0')*10+(buf[7]-'0');
				new_month=(buf[9]-'0')*10+(buf[10]-'0');
				goto new_log;
			} else if( rc==4 &&
				isdigit(buf[0]) && isdigit(buf[1]) &&
				isdigit(buf[2]) && isdigit(buf[3]) )

			{
				new_day=(buf[0]-'0')*10+(buf[1]-'0');
				new_month=(buf[2]-'0')*10+(buf[3]-'0');
				goto new_log;
			}
			write_cdr:
			if( cur_logfile==NULL ) {
				put_cdr_to_q( buf );
				break;
			}
			my_fputs( buf,cur_logfile );
			my_fputs( "\n",cur_logfile );
			my_fflush( cur_logfile );
			break;
		case LT_MERLIN:
			if( isalpha(buf[0]) && buf[1]==' ' ) {
				ParseClassicDate( buf+2 );
			}
			goto write_cdr;
		case LT_PANASONIC:
			ParseClassicDate( buf );
			goto write_cdr;
		case LT_GHX:
			if( rc>21 ) ParseClassicDate( buf+21 );
			goto write_cdr;
		case LT_MD110:
			if( rc>9 ) ParseMD110DateF2( buf+strspn(buf," ") );
			goto write_cdr;
		case LT_MERIDIAN:
			if( rc>=39 && isdigit(buf[25]) && isdigit(buf[26]) &&
				buf[27]=='/' &&
				isdigit(buf[28]) && isdigit(buf[29]) &&
				buf[30]==' ' &&
				isdigit(buf[31]) && isdigit(buf[32]) &&
				buf[33]==':' &&
				isdigit(buf[34]) && isdigit(buf[35]) &&
				buf[36]==':' &&
				isdigit(buf[37]) && isdigit(buf[38]) )
			{ // NEW CDR format
				int new_month,new_day;
				new_day=(buf[28]-'0')*10+(buf[29]-'0');
				new_month=(buf[25]-'0')*10+(buf[26]-'0');
				open_cur_logfile_with_check(1);
			} else { // OLD CDR format
				static int date_offs[] = { 25, 37, 39, 49, -1 };
				int i;
				for( i=0; date_offs[i]!=(-1); i++ ) {
					if( rc<(date_offs[i]+12) ) continue;
					dt=buf+date_offs[i];
					if( isdigit(dt[0]) && isdigit(dt[1]) &&
						dt[2]=='/' &&
						isdigit(dt[3]) && isdigit(dt[4]) &&
						dt[5]==' ' &&
						isdigit(dt[6]) && isdigit(dt[7]) &&
						dt[8]==':' &&
						isdigit(dt[9]) && isdigit(dt[10]) )
					{
						int new_month,new_day;
						new_day=(dt[3]-'0')*10+(dt[4]-'0');
						new_month=(dt[0]-'0')*10+(dt[1]-'0');
						open_cur_logfile_with_check(1);
						break;
					}
				}
			}
			goto write_cdr;
		}
	}
	return 0;
}
