/*

    (C) Alexey V. Kuznetsov, avk@gamma.ru, 2001, 2002
    changed by Denis CyxoB www.yamiyam.dp.ua
    and Andrew Kornilov andy[at]eva.dp.ua
    for the ATSlog version @version@ build @buildnumber@ www.atslog.dp.ua

*/

#include <ctype.h>
#include <stdio.h>
#include <stdarg.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <errno.h>
#include <fcntl.h>
#include <sys/wait.h>
#include <tcpd.h>

#include <signal.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#include <termios.h>
#include <sysexits.h>

#ifndef GETOPT
#include <unistd.h>
#endif

typedef long HANDLE;
typedef long DWORD;
#define INVALID_HANDLE_VALUE (HANDLE)(-1)

#define	MAXSTRINGLEN	1028
#define MAXPATHLEN	256
#define MAXFILENAMELEN	256
#define MAXERRORLEN	256


#define	SPACE_TO_ZERO(x)	((x)==' ' ? '0' : (x))

HANDLE h2close=INVALID_HANDLE_VALUE;

char dbg=0;
char copy_to_stderr=0;
char copy_to_stdout=0;
char by_month=0;
char swap_day_month=0;
FILE *errout;
FILE *pfd=NULL;
char *pid_file="/var/run/atslogd.pid";
char dirname[MAXPATHLEN+1+MAXFILENAMELEN+1];
char filename[MAXFILENAMELEN+1]="raw";
int dirlen=0;
int gpid;
int filenamelen=0;
// count of childrens
int childcount=0;
int is_tcp=0;
// default maximum clients for TCP port
int maxtcpclients=1;

#define LT_RAW		0
#define LT_DEFINITY	1
#define LT_MERLIN	2
#define LT_PANASONIC	3
#define LT_MERIDIAN	4
#define LT_GHX		5
#define LT_MD110	6

struct String {
	char		*s;
	struct String	*next;
};

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

static int daemonize( void )
{
	int rc;
	int rc_gpid;
	rc = fork();
	if( rc==(-1) ) return (-1);
	if( rc!=0 ) _exit(EX_OK);
	rc_gpid=setsid();
	if( rc_gpid==(-1) ) return (-1);
	return rc_gpid;
}

FILE *open_pid()
{
	FILE *fd=NULL;
	fd = fopen(pid_file, "w");
	return fd; 
}

void close_pid()
{
	if (pfd!=NULL)
		unlink(pid_file);
}

void my_exit(int code)
{
	close_pid();
	exit(code);
}

int my_syslog( char *s, ... )
{
	va_list va;
	time_t tm=time(NULL);
        struct tm lt;
	int l=strlen(s)-1;
        char db[100];
        static char fmtstr[25] = { "%a %b %d %H:%M:%S %Z %Y" };
        lt = *localtime(&tm);
        (void)strftime(db, sizeof(db), fmtstr, &lt);

	va_start( va,s );

	(void)fprintf( errout, "%s atslogd[%d]: ",db,getpid());
	if( copy_to_stderr ) (void)fprintf( stderr, "%s atslogd[%d]: ",db,getpid());
	if( s[l]=='\n' ) s[l]=0;
	(void)vfprintf( errout,s,va );
	(void)fprintf( errout,"\n" );
	if( copy_to_stderr ) {
		(void)vfprintf( stderr,s,va );
		(void)fprintf( stderr,"\n" );
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
		my_exit( 1 );
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
	pid_t pid;
	if( h2close!=INVALID_HANDLE_VALUE ) {
		my_syslog( "closing CDR stream" );
		close( h2close );
	}
	pid=waitpid(-1,NULL,WNOHANG);
	my_syslog( "exiting on signal %d",sig );
	my_exit( 0 );
}

static void sighuphandler(int sig)
{
	my_syslog( "catch SIGHUP, recreate logfile");
	if( cur_logfile!=NULL )
		{
			fclose( cur_logfile );
			cur_logfile=NULL;
		}

	memcpy( dirname+dirlen,filename,strlen(filename));
	if( (cur_logfile=fopen(dirname,"at"))==NULL )
	{
	    my_syslog( "can't open CDR file '%s': %s",dirname,strerror(errno) );
	    my_exit(1);
	}
}

static void sigchldhandler(int sig)
{
	pid_t pid;
	my_syslog( "catch SIGCHLD, waiting for childs");
	pid=waitpid(-1,NULL,WNOHANG);
	childcount--;
//	if (pid>0)
//	{
//		my_syslog( "child [%d]",);
//	}
}

static void setsighandler(int sig )
{
	struct sigaction sa;
	sigset_t ss;
	sigemptyset( &ss );
	sigaddset( &ss,sig );
	if( sigprocmask( SIG_UNBLOCK,&ss,NULL )==(-1) ) {
		my_syslog( "can't unblock signal %d: %s",sig,strerror(errno) );
		my_exit( 1 );
	}
	memset( &sa,0,sizeof(sa) );
	sa.sa_flags = SA_RESTART;
	if ( sig == SIGHUP)
		sa.sa_handler=sighuphandler;
	else if ( sig == SIGCHLD)
		sa.sa_handler=sigchldhandler;
	else
		sa.sa_handler=sighandler;
	if( sigaction( sig,&sa,NULL )==(-1) ) {
		my_syslog( "can't set signal handler on %d: %s",sig,strerror(errno) );
		my_exit( 1 );
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
	sprintf( ret_err,"(%d) ",errno );
	l=strlen(ret_err);
	strncpy( ret_err+l,strerror(errno),MAXERRORLEN-l );
	ret_err[MAXERRORLEN]=0;
	rc=strlen(ret_err+l);
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
		my_exit( 1 );
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


HANDLE open_tty( char *tty_name )
{
	HANDLE hCom;

	hCom = open( tty_name,O_RDWR );

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
		{ 1200, B1200 },
		{ 2400, B2400 },
		{ 4800, B4800 },
		{ 9600, B9600 },
		{ 19200, B19200 },
		{ 38400, B38400 },
		{ 57600, B57600 },
		{ 115200, B115200 },
		{ 0, 0 }
	};

	struct Speed *sp;

	struct termios tt;

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

	return 0;
}

int read_string( HANDLE hCom,char *buf,int blen )
{
	DWORD dwLength;
	int count;

	for( count=0; count<blen; count++,buf++ ) {
		do {
			while( (dwLength=read( hCom,buf,1 ))>=0 ) {
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
			if( dwLength==(-1) ) {
				my_syslog( "read error: %s",my_strerror() );
			}
		} while( dwLength<=0 );
	}
	return count;
}

int auth_libwrap(struct sockaddr_in sa_rem)
{
	struct request_info req;
	request_init(&req,RQ_DAEMON,"atslogd",RQ_CLIENT_SIN,&sa_rem,0);
	fromhost(&req);
	if (!hosts_access(&req))
	{
			my_syslog( "connection from host %s refused by libwrap",inet_ntoa(sa_rem.sin_addr));
			return 0;
	}
	return 1;

}



HANDLE open_io( char *io_name,long speed,int data_bits,char parity,int stop_bits )
{
	HANDLE hCom;
	int rc;

		hCom = open_tty( io_name );
		if (hCom == INVALID_HANDLE_VALUE) {
			my_syslog( "can't open serial device '%s'",io_name );
			return INVALID_HANDLE_VALUE;
		}
		rc = set_tty_params( hCom,speed,data_bits,parity,stop_bits );
		if( rc==(-1) ) {
			my_syslog( "Unable to set TTY device parameters" );
			return INVALID_HANDLE_VALUE;
		}
	h2close=hCom;
	return hCom;
}

void usage( void )
{
	(void)fprintf( stderr,
"CDR Reader for PBXs v.%s (C) Alexey V. Kuznetsov, avk[at]gamma.ru, 2001-2002\n"
"changed by Denis CyxoB www.yamiyam.dp.ua\n"
"and Andrew Kornilov andy[at]eva.dp.ua\n"
"for the ATSlog version @version@ build @buildnumber@ www.atslog.dp.ua\n"
"\n"
"atslogd [-D dir] [-L logfile] [-s speed] [-c csize] [-p parity] [-f sbits]\n"
"        [-t type] [-P PIDfile][-d] [-e] [-a] [-o] [-i address] [tcp:port] dev\n"
"-D dir\t\tdirectory where CDR files will be put, default is current dir\n"
"-L logfile\tfile for error messages, default is stderr\n"
"-F filename\tname of file where CDR messages will be put\n"
"-s speed\tspeed of serial device, default 9600\n"
"-c char_size\tlength of character; valid values from 5 to 8\n"
"-p parity\tparity of serial device:\n"
"\t\te - even parity, o - odd parity, n - no parity,\n"
"-f stop_bits\tnumber of stop bits; valid values 1 or 2\n"
"-d\t\toutput additional debug messages\n"
"-e\t\tcopy error messages to stderr (in case if -L has value)\n"
"-a\t\twrite date at the beginning of file (for Definity type only)\n"
"-o\t\twrite CDR additionally to stdout\n"
"-m\t\twrite log files on month-by-month instead of day-by-day basis\n"
"-n\t\tconsider day in place of month and vice versa\n"
"-x number\tmaximum number of clients for TCP connections (default: 1)\n"
"\t\tsee /etc/hosts.allow, /etc/hosts.deny)\n"
"-w seconds\ttimeout before I/O port will be opened next time after EOF\n"
"-i address\tIP address of interface for bind only to it\n"
"\t\t(default to all interfaces)\n"
"tcp:port\twhere port is a TCP port for listen on.\n"
"-b\t\tbecome daemon\n"
"-P\t\tPID file. /var/run/atslogd.pid by default\n"
"\n"
"Use libwrap for contol access to TCP connections. See /etc/hosts.allow\n"
"and /etc/hosts.deny\n",CDRR_VER);

my_exit(1);
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
	char write_date=0;
	// move tcp section here
	// 
	unsigned short tcpPort=0;
	char *hostname=NULL;
	
	int opt=1;
	int pid;
	HANDLE s,new_s;
	struct sockaddr_in sa_loc,sa_rem;
	socklen_t sa_rem_len;
	//
	char do_daemonize=0;
	sigset_t ss;
	
	HANDLE hCom;

	while( (rc=getopt(argc,argv,"boahdemnws:D:L:P:F:p:c:f:r:x:i:"))!=(-1) ) {
		switch( rc ) {
		case 'D':
			dirlen=strlen(optarg);
			if( dirlen>MAXPATHLEN ) {
				(void)fprintf( stderr,"Too long directory name\n" );
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
		case 'F':
			filenamelen=strlen(optarg);
			if( filenamelen>MAXFILENAMELEN ) {
				(void)fprintf( stderr,"Too long file name\n" );
				return 1;
			}
			memcpy( filename,optarg,filenamelen );
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
		case 'x':
			maxtcpclients=atoi(optarg);
			break;
		case 'i':
			hostname=optarg;
			break;
		case 'w':
			next_open_timeout=atoi(optarg);
			break;
		case 'b':
			do_daemonize=1;
			break;
		case 'P':
			pid_file=optarg;
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
			(void)fprintf( stderr,"Unknown switch: %c\n",(char)rc );
			my_exit( 1 );
		}
	}

	argc -= optind;
	argv += optind;

	if( logfile ) {
		errout=fopen( logfile,"at" );
		if( errout==NULL ) {
			(void)fprintf( stderr,"Can't open '%s': %s\n",logfile,strerror(errno) );
			return 1;
		}
	} else {
		errout=stderr;
		copy_to_stderr=0;
	}

	if( argc<=0 ) {
		my_syslog( "No input TTY device given" );
		usage();
	}

	my_syslog( "starting" );

	gpid = daemonize();

	if( do_daemonize && gpid==(-1) ) {
		my_syslog( "can't become daemon, exiting" );
		my_exit( 1 );
	}

	pfd = open_pid();
	if (pfd!=NULL) {
	    (void)fprintf(pfd, "%ld\n", (long)gpid);
	    fclose(pfd);
	}else{
	    my_syslog( "can't write PID file, exiting" );
	    my_exit( 1 );
	}

	sigfillset( &ss );
	if( sigprocmask( SIG_SETMASK,&ss,NULL )==(-1) ) {
		my_syslog( "can't block all signals: %s",strerror( errno ) );
		my_exit( 1 );
	}
	setsighandler( SIGHUP );
	setsighandler( SIGINT );
	setsighandler( SIGTERM );
	setsighandler( SIGCHLD );

	if( strncasecmp( argv[0],"tcp:",4 )==0 ) {
		tcpPort=atoi(argv[0]+4);
		if( tcpPort==0 ) {
			my_syslog( "Invalid TCP port number" );
			hCom=INVALID_HANDLE_VALUE;
		}
		else
		{
			s=socket(PF_INET,SOCK_STREAM,0);
			if(s==INVALID_HANDLE_VALUE) {
				my_syslog( "socket() failed: %s",my_strerror() );
				return s;
			}
			if (setsockopt(s,SOL_SOCKET,SO_REUSEADDR,(char *)&opt,sizeof(opt)) < 0)
			{
				my_syslog( "sotsockopt() failed: %s",my_strerror() );
				return INVALID_HANDLE_VALUE;
			}
			// send keepalive packets. if remote end hangs then we'll can know
			// this ?
			if (setsockopt(s,SOL_SOCKET,SO_KEEPALIVE,(char *)&opt,sizeof(opt)) < 0)
			{
				my_syslog( "sotsockopt() failed: %s",my_strerror() );
				return INVALID_HANDLE_VALUE;
			}
			h2close=s;
			memset( &sa_loc,0,sizeof(sa_loc) );
			memset( &sa_rem,0,sizeof(sa_loc) );
			sa_rem.sin_family=sa_loc.sin_family=AF_INET;
			sa_loc.sin_port=htons(tcpPort);
			if (hostname!=NULL)
			{
				if (inet_aton(hostname,&sa_loc.sin_addr)==0)
				{
					my_syslog( "bind() on IP %s failed: %s",hostname,my_strerror() );
					my_exit(1);
				}
			}
			
			if( bind(s,(struct sockaddr*)&sa_loc,sizeof(sa_loc) )==(-1) ) {
				if (hostname!=NULL)
					my_syslog( "bind() on port %s:%d failed: %s",hostname,tcpPort,my_strerror() );
				else
					my_syslog( "bind() on port %d failed: %s",tcpPort,my_strerror() );
				goto err_ret;
			}
			if( listen(s,5)==(-1) ) {
				my_syslog( "listen() failed: %s",my_strerror() );
				goto err_ret;
			}
			if (hostname!=NULL)
				my_syslog( "waiting TCP connection on port %s:%d",hostname,tcpPort );
			else
				my_syslog( "waiting TCP connection on port %d",tcpPort );
			for( ;; ) {
				sa_rem_len=sizeof(sa_rem);
				if( (new_s=accept(s,(struct sockaddr*)&sa_rem,&sa_rem_len ))==(-1) ) {
					my_syslog( "accept() failed: %s",my_strerror() );
					err_ret:
					h2close=INVALID_HANDLE_VALUE;
					hCom=h2close;
					close(s);
					break;
				}
				if ( childcount >= maxtcpclients )
				{
					my_syslog( "connection from %s:%d refused because maximum number of clients [%d] has been reached",inet_ntoa(sa_rem.sin_addr),ntohs(sa_rem.sin_port),maxtcpclients );
					close( new_s );
					h2close=INVALID_HANDLE_VALUE;
					hCom=h2close;
					sleep(2);
					continue;
				}
				else
					my_syslog( "connection from %s:%d",inet_ntoa(sa_rem.sin_addr),ntohs(sa_rem.sin_port) );
				// using libwrap for controll access
				if( !auth_libwrap(sa_rem) ) 
				{
					h2close=INVALID_HANDLE_VALUE;
					hCom=h2close;
					close( new_s );
					continue;
				}
				else
				{
					pid=fork();
					if (pid==0)
					{
						// now we are the champions...errr...child ;-)
						
						// close input socket
						close(s);
						// temp. disable handler to revent closing NULL in signal
						// handler
						h2close=INVALID_HANDLE_VALUE;
						hCom=new_s;
						break;
					}
					else if (pid==-1)
					{
						// can't fork. system error
						switch(errno)
						{
							case EAGAIN:
								{
									// resource temp. unavail. try again later ;-)
									my_syslog( "fork() failed: %s",my_strerror() );
									h2close=INVALID_HANDLE_VALUE;
									hCom=h2close;
									close(new_s);
									close(s);
									//continue;
									my_exit(1);
								};
							case ENOMEM:
								{
									// not enough system memory. hangup?
									my_syslog( "fork() failed: %s",my_strerror() );
									h2close=INVALID_HANDLE_VALUE;
									hCom=h2close;
									close(new_s);
									close(s);
									//continue;
									my_exit(1);
								};
						}			
					}
					else
					{
						// parent code
						
						// close child socket 
						childcount++;
						h2close=INVALID_HANDLE_VALUE;
						hCom=h2close;
						close(new_s);
						// continue accepting connection
						continue;
					}
				}
			}
	

			///
			if (hCom == INVALID_HANDLE_VALUE) {
				if (hostname!=NULL)
					my_syslog( "can't open '%s:%s'",hostname,tcpPort );
				else
					my_syslog( "can't open '%s'",tcpPort );

				hCom=INVALID_HANDLE_VALUE;
				h2close=hCom;
			}
			else
			{
				h2close=hCom;
			}
		}
	}
	else
		hCom = open_io( argv[0],speed,data_bits,parity,stop_bits );

	if( hCom==INVALID_HANDLE_VALUE ) {
		my_syslog( "can't open '%s', exiting",argv[0] );
		return 1;
	}

	memcpy( dirname+dirlen,filename,strlen(filename));
	if( (cur_logfile=fopen(dirname,"at"))==NULL ){
		my_syslog( "can't open CDR file '%s': %s",dirname,strerror(errno) );
		return 1;
	}

	while( (rc=read_string( hCom,buf,MAXSTRINGLEN ))>=0 ) {
		if( rc==0 ) {
			if( strncasecmp( argv[0],"tcp:",4 )==0 ) {
				// because we read() in blocking mode so if we've got 0 ->
				// remote peer hangs
				if (errno != EINTR)
				{
					my_syslog( "connection with remote peer %s:%d has been closed, exiting",inet_ntoa(sa_rem.sin_addr),ntohs(sa_rem.sin_port));
					h2close=INVALID_HANDLE_VALUE;
					close( hCom );
					my_exit(0);
					
				}
			}
			else
			{
				h2close=INVALID_HANDLE_VALUE;
				close( hCom );
				sleep( next_open_timeout );
				hCom = open_io( argv[0],speed,data_bits,parity,stop_bits );
				if( hCom==INVALID_HANDLE_VALUE ) {
					my_syslog( "can't open '%s', exiting",argv[0] );
					return 1;
				}
			}
			continue;
		}
		my_fputs( buf,cur_logfile );
		my_fputs( "\n",cur_logfile );
		my_fflush( cur_logfile );
		if( cur_logfile==NULL ) {
			put_cdr_to_q( buf );
			break;
		}
	}
	return 0;
}
