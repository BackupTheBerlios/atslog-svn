/*
 * Original - (c) Alexey V. Kuznetsov, avk@gamma.ru, 2001, 2002 CDR Reader
 * for PBXs
 * 
 * Modifications for the ATSlog project:  Andrew
 * Denis CyxoB www.yamiyam.dp.ua
 * Andrew Kornilov <akornilov@gmail.com>
 * Alex Samorukov <samm@os2.kiev.ua> RFC 854
 * WILL/WONT DO/DONT negotiation is based on the BSD netcat
 * 
 * ATSlog version @version@ build @buildnumber@ www.atslog.com
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
#include "atslogd.h"
#ifdef USE_LIBWRAP
#include <tcpd.h>
#endif				/* USE_LIBWRAP */
#include <netdb.h>

#include <signal.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <arpa/telnet.h>

#include <termios.h>
#include <sysexits.h>

#ifndef GETOPT
#include <unistd.h>
#endif


typedef long	HANDLE;
typedef long	DWORD;
#define INVALID_HANDLE_VALUE (HANDLE)(-1)

#define	MAXSTRINGLEN	1028
#define MAXPATHLEN	256
#define MAXFILENAMELEN	256
#define MAXERRORLEN	256


#define	SPACE_TO_ZERO(x)	((x)==' ' ? '0' : (x))

HANDLE		h2close = INVALID_HANDLE_VALUE;

char		dbg = 0,lastchar='\0';
char		copy_to_stderr = 0;
char		copy_to_stdout = 0;
FILE           *errout;
FILE           *pfd = NULL;
char           *pid_file = "/var/run/atslogd.pid";
char           *logfile = NULL;
char		dirname   [MAXPATHLEN + 1 + MAXFILENAMELEN + 1];
char		filename  [MAXFILENAMELEN + 1] = "raw";
int		dirlen = 0;
int		pid = 0;
int		filenamelen = 0;
/* count of childrens */
int		childcount = 0;
/* default maximum clients for TCP port */
int		maxtcpclients = 1;
/* startup tty settings */
struct termios	oldtio;
int		is_tcp = 0, is_rtcp = 0;
int		tflag = 0;	/* Telnet Emulation */

#define LT_RAW		0
#define LT_DEFINITY	1
#define LT_MERLIN	2
#define LT_PANASONIC	3
#define LT_MERIDIAN	4
#define LT_GHX		5
#define LT_MD110	6

struct String {
	char           *s;
	struct String  *next;
};

FILE           *cur_logfile = NULL;
int		cur_month = 0, new_month = 0;
int		cur_day = 0, new_day = 0;
struct String  *cdrs = NULL;
struct String  *cdrs_last = NULL;

#ifdef GETOPT

char           *optarg = NULL;
int		optind = 1;

static int 
getopt(int argc, char *argv[], char *sw)
{
	char           *s, sym;
	if (optind >= argc)
		return (-1);
	if (argv[optind][0] != '-' && argv[optind][0] != '/')
		return (-1);
	sym = argv[optind][1];
	s = strchr(sw, sym);
	optind++;
	if (s == NULL || *s == 0) {
		return sym;
	}
	if (s[1] == ':') {
		if (optind >= argc) {
			optarg = NULL;
		} else {
			optarg = argv[optind];
			optind++;
		}
	}
	return sym;
}

#else

extern char    *optarg;
extern int	optind;

#endif

void 
close_tty(HANDLE hCom)
{
	/* restore tty settings and close serial port */
	tcsetattr(hCom, TCSANOW, &oldtio);
	close(hCom);
}

static int 
daemonize(void)
{
	int		rc;
	rc = fork();
	if (rc == (-1))
		return (-1);
	if (rc != 0)
		_exit(EX_OK);
	return rc;
}

FILE           *
open_pid()
{
	FILE           *fd = NULL;
	fd = fopen(pid_file, "w");
	return fd;
}

void 
close_pid()
{
	if (pfd != NULL)
		unlink(pid_file);
	pfd = NULL;
}

void 
my_exit(int code)
{
	close_pid();
	exit(code);
}

int 
my_syslog(char *s,...)
{
	va_list		va;
	time_t		tm = time(NULL);
	struct tm	lt;
	int		l = strlen(s) - 1;
	char		db        [100];
	static char	fmtstr[25] = {"%a %b %d %H:%M:%S %Z %Y"};
	lt = *localtime(&tm);
	(void)strftime(db, sizeof(db), fmtstr, &lt);

	va_start(va, s);

	(void)fprintf(errout, "%s atslogd[%d]: ", db, getpid());
	if (copy_to_stderr)
		(void)fprintf(stderr, "%s atslogd[%d]: ", db, getpid());
	if (s[l] == '\n')
		s[l] = 0;
	(void)vfprintf(errout, s, va);
	(void)fprintf(errout, "\n");
	if (copy_to_stderr) {
		(void)vfprintf(stderr, s, va);
		(void)fprintf(stderr, "\n");
	}
	fflush(errout);
	if (copy_to_stderr)
		fflush(stderr);
	return 0;
}

static void    *
my_malloc(int size, char do_clr)
{
	void           *p = malloc(size);
	if (p == NULL) {
		my_syslog("Can't allocate %d bytes: %s", strerror(errno));
		my_exit(1);
	}
	if (do_clr)
		memset(p, 0, size);
	return p;
}

static char    *
my_strdup(char *s)
{
	int		l = strlen(s) + 1;
	char           *p;
	p = my_malloc(l, 0);
	memcpy(p, s, l);
	return p;
}

static void 
my_write(unsigned char *s, FILE * fp, int len)
{
	int		i;
	for (i = 0; i < len; i++) {
		/* strip  0x02 (STX) characters (NEC) */
		if (s[i] == 0x02)
			continue;
		/* replace 0x03 (ETX) with \n for the NEC PBX */
		/* Some PBX do only \r as line terminators */
		if (s[i] == 0x03 ||  s[i] == '\r')
			s[i] = '\n';
		if(s[i]=='\n' && lastchar=='\n') /* skip blank lines */
			continue;
		fputc(s[i], fp);
		if (copy_to_stdout) {
			fputc(s[i], stdout);
		}
		lastchar=s[i];
	}
}

static void 
my_fflush(FILE * fp)
{
	fflush(fp);
	if (copy_to_stdout) {
		fflush(stdout);
	}
}

static void 
sighandler(int sig)
{
	if (h2close != INVALID_HANDLE_VALUE) {
		my_syslog("Closing CDR stream");
		if ((is_tcp) || (is_rtcp)) {
			close(h2close);
		} else
			close_tty(h2close);
	}
	waitpid(-1, NULL, WNOHANG);
	my_syslog("Exiting on signal %d", sig);
	my_exit(0);
}

static void 
sighuphandler(int sig)
{
	my_syslog("Catch SIGHUP(%d), recreate logfile", sig);
	
	if (cur_logfile != NULL) {
		fclose(cur_logfile);
		cur_logfile = NULL;
	}
	
	memcpy(dirname + dirlen, filename, strlen(filename));
	if ((cur_logfile = fopen(dirname, "at")) == NULL) {
		my_syslog("Can't open CDR file '%s': %s", dirname, strerror(errno));
		my_exit(1);
	}
	
	if (errout && errout!=stderr) {
		fclose(errout);
		if ((errout = fopen(logfile, "at")) == NULL) {
			my_syslog("Can't open log file '%s': %s", logfile, strerror(errno));
			my_exit(1);
		}
	}
	
	
}

static void 
sigchldhandler(int sig)
{
	my_syslog("Catch SIGCHLD, waiting for childs");
	waitpid(-1, NULL, WNOHANG);
	childcount--;
}

static void 
setsighandler(int sig)
{
	struct sigaction sa;
	sigset_t	ss;
	sigemptyset(&ss);
	sigaddset(&ss, sig);
	if (sigprocmask(SIG_UNBLOCK, &ss, NULL) == (-1)) {
		my_syslog("Can't unblock signal %d: %s", sig, strerror(errno));
		my_exit(1);
	}
	memset(&sa, 0, sizeof(sa));
	sa.sa_flags = SA_RESTART;
	if (sig == SIGHUP)
		sa.sa_handler = sighuphandler;
	else if (sig == SIGCHLD)
		sa.sa_handler = sigchldhandler;
	else
		sa.sa_handler = sighandler;
	if (sigaction(sig, &sa, NULL) == (-1)) {
		my_syslog("Can't set signal handler on %d: %s", sig, strerror(errno));
		my_exit(1);
	}
}

char           *
my_strerror(void)
{
	static char	ret_err[MAXERRORLEN + 1];
	int		rc;
	int		l;
	sprintf(ret_err, "(%d) ", errno);
	l = strlen(ret_err);
	strncpy(ret_err + l, strerror(errno), MAXERRORLEN - l);
	ret_err[MAXERRORLEN] = 0;
	rc = strlen(ret_err + l);
	if (rc == 0) {
		ret_err[l - 1] = 0;
	} else {
		if (ret_err[l + rc - 1] == '\n')
			ret_err[l + rc - 1] = 0;
	}
	return ret_err;
}


HANDLE 
open_tty(char *tty_name)
{
	HANDLE		hCom;
	struct termios	newtio;

	/*
	 * we need to open tty in non blocking mode to set CLOCAL value, and
	 * then reopen it in normal mode to prevent waiting for CARRIER line
	 * on opening /dev/ttySx devices
	 */
	hCom = open(tty_name, O_RDWR | O_NONBLOCK);
	if (hCom == INVALID_HANDLE_VALUE) {
		my_syslog("open_tty on '%s' failed: %s", tty_name, my_strerror());
		return hCom;
	}
	tcgetattr(hCom, &oldtio);
	bzero(&newtio, sizeof(newtio));
	newtio.c_cflag = B9600 | CS8 | CLOCAL | CREAD | CSTOPB;
	newtio.c_iflag = IGNPAR;
	newtio.c_oflag = 0;
	newtio.c_lflag = 0;
	tcflush(hCom, TCIFLUSH);
	tcsetattr(hCom, TCSANOW, &newtio);
	close(hCom);

	/* reopen in blocking mode */
	hCom = open(tty_name, O_RDWR);

	if (hCom == INVALID_HANDLE_VALUE) {
		my_syslog("open_tty on '%s' failed: %s", tty_name, my_strerror());
	}
	return hCom;
}

int 
set_tty_params(HANDLE hCom, long speed, int data_bits, char parity, int stop_bits)
{
	static struct Speed {
		long		speed;
		DWORD		wspeed;
	}		speeds       [] = {
		{
			1200, B1200
		},
		{
			2400, B2400
		},
		{
			4800, B4800
		},
		{
			9600, B9600
		},
		{
			19200, B19200
		},
		{
			38400, B38400
		},
		{
			57600, B57600
		},
		{
			115200, B115200
		},
		{
			0, 0
		}
	};

	struct Speed   *sp;

	struct termios	tt;

	for (sp = speeds; sp->speed; sp++) {
		if (sp->speed == speed)
			break;
	}
	if (sp->speed == 0) {
		my_syslog("Invalid speed: %ld", speed);
		return (-1);
	}
	if (stop_bits != 1 && stop_bits != 2) {
		my_syslog("Invalid number of stop bits: %d", stop_bits);
		return (-1);
	}
	if (data_bits < 5 || data_bits > 8) {
		my_syslog("Invalid number of data bits: %d", data_bits);
		return (-1);
	}
	cfmakeraw(&tt);
	tt.c_iflag = 0;
	tt.c_oflag = 0;
	tt.c_lflag = 0;
	tt.c_cflag = CREAD | CLOCAL | CRTSCTS | HUPCL;
	switch (data_bits) {
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
	if (stop_bits == 2)
		tt.c_cflag |= CSTOPB;
	switch (parity) {
	case 'e':
		tt.c_cflag |= PARENB;
		break;
	case 'o':
		tt.c_cflag |= PARENB | PARODD;
		break;
	case 0:
	case 'n':
		break;
	default:
		my_syslog("Invalid parity: '%c'", parity);
		return (-1);
	}
	/*
	 * read() will return when VMIN bytes of input are available, or if
	 * interrupted. Otherwise it will wait indefinitely.
	 */
	tt.c_cc[VTIME] = 0;
	tt.c_cc[VMIN] = 1;
	cfsetspeed(&tt, sp->wspeed);
	if (tcsetattr(hCom, TCSANOW, &tt) == (-1)) {
		my_syslog("tcsetattr failed: %s", my_strerror());
		return (-1);
	}
	return 0;
}


/*
 * ensure all of data on socket comes through. f==read || f==write
 */
ssize_t
atomicio(ssize_t(*f) (int, void *, size_t), int fd, void *_s, size_t n){
	char           *s = _s;
	ssize_t		res   , pos = 0;

	while (n > pos) {
		res = (f) (fd, s + pos, n - pos);
		switch (res) {
		case -1:
			if (errno == EINTR || errno == EAGAIN)
				continue;
		case 0:
			return (res);
		default:
			pos += res;
		}
	}
	return (pos);
}


int 
read_block(HANDLE hCom, unsigned char *buf)
{
	unsigned char	obuf[4];
	unsigned char  *p, *end;
	int		count     , stop;
	do {
		stop = 1;
		count = read(hCom, buf, sizeof(count));
		if(dbg)
			my_syslog("read(): %d bytes", count);
		if (tflag) {
			end = buf + count;
			obuf[0] = '\0';

			for (p = buf; p < end; p++) {
				if (*p != IAC)
					break;
				stop = 0;	/* we dont really need IAC
						 * block */
				obuf[0] = IAC;
				p++;
				if ((*p == WILL) || (*p == WONT))
					obuf[1] = DONT;
				if ((*p == DO) || (*p == DONT))
					obuf[1] = WONT;
				if (obuf) {
					p++;
					obuf[2] = *p;
					obuf[3] = '\0';
					if (atomicio((ssize_t(*) (int, void *, size_t)) write,
						     hCom, obuf, 3) != 3)
						my_syslog("telnet: Write Error");
					obuf[0] = '\0';
				}
			}
		}
	} while (!stop);
	return count;
}

#ifdef USE_LIBWRAP
int 
auth_libwrap(int socketfp)
{
	struct request_info req;
	request_init(&req, RQ_DAEMON, "atslogd", RQ_FILE, socketfp, 0);
	fromhost(&req);
	if (!hosts_access(&req)) {
		my_syslog("Connection refused by libwrap");
		return 0;
	}
	return 1;
}
#endif				/* USE_LIBWRAP */


HANDLE 
open_io(char *io_name, long speed, int data_bits, char parity, int stop_bits)
{
	HANDLE		hCom;
	int		rc;

	hCom = open_tty(io_name);
	if (hCom == INVALID_HANDLE_VALUE) {
		my_syslog("Can't open serial device '%s'", io_name);
		return INVALID_HANDLE_VALUE;
	}
	rc = set_tty_params(hCom, speed, data_bits, parity, stop_bits);
	if (rc == (-1)) {
		my_syslog("Unable to set TTY device parameters");
		return INVALID_HANDLE_VALUE;
	}
	h2close = hCom;
	return hCom;
}

void 
usage(FILE * fp)
{
	(void)fprintf(fp,
		      "ATSlog version @version@ build @buildnumber@ www.atslog.com\n"
		      "Usage: atslogd [-D dir] [-L logfile] [-F filename] [-s speed] [-c csize]\n"
		      "               [-p parity] [-f sbits] [-d] [-e] [-o] [-x number] [-w seconds]\n"
		      "               [-b] [-P pidfile] tcp[:address]:port|rtcp:address:port|dev\n"
		      "Options:\n"
		      " -D directory     Specify  a location where atslogd should place CDR log files,\n"
		      "                  default is current dir\n"
		      " -L logfile       file for error messages, default is stderr\n"
		      " -F filename      name of file where CDR messages will be put, default is 'raw'\n"
		      " -s speed         tspeed of the serial device, default 9600\n"
		      " -c char_size     length of the character; valid values from 5 to 8\n"
		      " -p parity        parity of the serial device:\n"
		      "                  e - even parity, o - odd parity, n - no parity,\n"
		      " -f stop_bits     number of the stop bits; valid values 1 or 2\n"
		      " -d               operate in DEBUG mode\n"
		      " -e               copy error messages to stderr (in case if -L has value)\n"
		      " -o               write CDR additionally to stdout\n"
		      " -x connections   number of the allowed TCP clients. Default is 1\n"
		      " -t               emulation of the TELNET protocol\n"
		      " -w seconds       timeout before I/O port will be opened next time after EOF;\n"
		      "                  default is 5\n"
		      " -b               daemonize on startup\n"
		      " -P pid-file      Specify an alternative file in which to store the process ID.\n"
		      "                  /var/run/atslogd.pid by default\n"
		      " tcp[:host]:port  IP address and TCP port to listen for the incoming\n"
		      "                  connections. You may omit address and daemon will bind\n"
		      "                  on all available IP addresses\n"
		      " rtcp:host:port   Remote IP address and TCP port to connect\n"
		      " dev              serial device to use\n"
		      "\n"
#ifdef USE_LIBWRAP
		      "You can use libwrap to limit access for the tcp connections.\n"
		      "See /etc/hosts.allow and /etc/hosts.deny\n\n");
#else				/* USE_LIBWRAP */
		      );
#endif				/* USE_LIBWRAP */

	my_exit(1);
}



int 
main(int argc, char *argv[])
{
	int		rc;
	long		speed = 9600;
	int		data_bits = 8, stop_bits = 1;
	char		parity = 0;
	int		next_open_timeout = 5;
	unsigned char		buf       [MAXSTRINGLEN + 1];
	unsigned short	tcpPort = 0, rtcpPort = 0;
	char           *hostname = NULL, *rhostname = NULL;
	char           *token = NULL, *saveptr = NULL, *port = NULL;

	int		opt = 1;
	int		pid;
	HANDLE		s      , new_s;

	struct sockaddr_in sa_lserver, sa_rclient;
	socklen_t	sa_rclient_len;
	struct hostent *he_rserver = NULL;
	struct hostent *he_lserver = NULL;

	char		do_daemonize = 0;
	sigset_t	ss;

	HANDLE		hCom;

	while ((rc = getopt(argc, argv, "tbohdes:D:L:P:F:p:c:f:x:w:s:")) != (-1)) {
		switch (rc) {
		case 'D':
			dirlen = strlen(optarg);
			if (dirlen > MAXPATHLEN) {
				(void)fprintf(stderr, "Too long directory name\n");
				my_exit(1);
			}
			if (dirlen == 0) {
				dirname[0] = '.';
				dirlen = 1;
			} else {
				memcpy(dirname, optarg, dirlen);
			}
			switch (dirname[dirlen - 1]) {
			case '/':
			case '\\':
				break;
			default:
				dirname[dirlen++] = '/';
				break;
			}
			break;
		case 'L':
			logfile = optarg;
			break;
		case 'F':
			filenamelen = strlen(optarg);
			if (filenamelen > MAXFILENAMELEN) {
				(void)fprintf(stderr, "Too long file name\n");
				my_exit(1);
			}
			memcpy(filename, optarg, filenamelen);
			break;
		case 's':
			speed = atol(optarg);
			break;
		case 't':
			tflag = 1;
			break;
		case 'p':
			parity = optarg[0];
			break;
		case 'c':
			data_bits = atoi(optarg);
			break;
		case 'f':
			stop_bits = atoi(optarg);
			break;
		case 'd':
			dbg = 1;
			break;
		case 'e':
			copy_to_stderr = 1;
			break;
		case 'x':
			maxtcpclients = atoi(optarg);
			break;
		case 'w':
			next_open_timeout = atoi(optarg);
			break;
		case 'b':
			do_daemonize = 1;
			break;
		case 'P':
			pid_file = optarg;
			break;
		case 'o':
			copy_to_stdout = 1;
			break;
		case 'h':
			usage(stdout);
		default:
			(void)fprintf(stderr, "Unknown switch: %c\n", (char)rc);
			my_exit(1);
		}
	}

	argc -= optind;
	argv += optind;

	if (logfile) {
		errout = fopen(logfile, "at");
		if (errout == NULL) {
			(void)fprintf(stderr, "Can't open '%s': %s\n", logfile, strerror(errno));
			my_exit(1);
		}
	} else {
		errout = stderr;
		copy_to_stderr = 0;
	}

	if (argc <= 0) {
		my_syslog("No input TTY device given");
		usage(stderr);
	}
	my_syslog("Starting");
	
	if (do_daemonize)
		daemonize();
	
	pid = getpid();
	
	if (do_daemonize && pid == (-1)) {
		my_syslog("Can't become daemon, exiting");
		my_exit(1);
	}
	pfd = open_pid();
	if (pfd != NULL) {
		(void)fprintf(pfd, "%ld\n", (long)pid);
		fclose(pfd);
	} else {
		my_syslog("Can't write to '%s' PID file, exiting", pid_file);
		my_exit(1);
	}
	
	if (do_daemonize)
		pid = daemonize();
	else
		pid = getpid();

	if (do_daemonize && pid == (-1)) {
		my_syslog("Can't become daemon, exiting");
		my_exit(1);
	}
	pfd = open_pid();
	if (pfd != NULL) {
		(void)fprintf(pfd, "%ld\n", (long)pid);
		fclose(pfd);
	} else {
		my_syslog("Can't write to '%s' PID file, exiting", pid_file);
		my_exit(1);
	}

	sigfillset(&ss);
	if (sigprocmask(SIG_SETMASK, &ss, NULL) == (-1)) {
		my_syslog("Can't block all signals: %s", strerror(errno));
		my_exit(1);
	}
	setsighandler(SIGHUP);
	setsighandler(SIGINT);
	setsighandler(SIGTERM);
	setsighandler(SIGCHLD);

	if (strchr(argv[0], ':') != NULL)
		token = (char *)strtok_r(argv[0], ":", &saveptr);
	if (token != NULL) {
		if (strcasecmp(token, "tcp") == 0) {
			is_tcp = 1;
			hostname = (char *)strtok_r(NULL, ":", &saveptr);
			if (hostname == NULL)
				usage(stderr);
			port = (char *)strtok_r(NULL, ":", &saveptr);
			if (port == NULL) {
				port = my_strdup(hostname);
				hostname = NULL;
			}
			if (hostname != NULL) {
				if ((he_lserver = gethostbyname(hostname)) == NULL) {
					my_syslog("Invalid hostname: %s", hostname);
					exit(1);
				}
			}
			tcpPort = atoi(port);
		} else if (strcasecmp(token, "rtcp") == 0) {
			is_rtcp = 1;
			rhostname = (char *)strtok_r(NULL, ":", &saveptr);
			if (rhostname == NULL)
				usage(stderr);
			port = (char *)strtok_r(NULL, ":", &saveptr);
			if (port == NULL)
				usage(stderr);
			rtcpPort = atoi(port);
		} else {
			my_syslog("Unknown token %s", token);
			my_exit(1);
		}

	}
	if (is_tcp)
		/* listen */
	{
		if (tcpPort == 0) {
			my_syslog("Invalid TCP port 0 to listen");
			hCom = INVALID_HANDLE_VALUE;
			my_exit(1);
		}
		s = socket(PF_INET, SOCK_STREAM, 0);
		if (s == INVALID_HANDLE_VALUE) {
			my_syslog("socket() failed: %s", my_strerror());
			my_exit(1);
		}
		if (setsockopt(s, SOL_SOCKET, SO_REUSEADDR, (char *)&opt, sizeof(opt)) < 0) {
			my_syslog("setsockopt() failed: %s", my_strerror());
			my_exit(1);
		}
		/*
		 * send keepalive packets. if remote end hangs then we'll can
		 * know this ?
		 */
		if (setsockopt(s, SOL_SOCKET, SO_KEEPALIVE, (char *)&opt, sizeof(opt)) < 0) {
			my_syslog("setsockopt() failed: %s", my_strerror());
			my_exit(1);
		}
		h2close = s;
		memset(&sa_lserver, 0, sizeof(sa_lserver));
		memset(&sa_rclient, 0, sizeof(sa_rclient));
		sa_rclient.sin_family = sa_lserver.sin_family = AF_INET;
		sa_lserver.sin_port = htons(tcpPort);
		if (hostname != NULL) {
			memcpy(&sa_lserver.sin_addr, he_lserver->h_addr_list[0], he_lserver->h_length);
		}
		if (bind(s, (struct sockaddr *)&sa_lserver, sizeof(sa_lserver)) == (-1)) {
			if (hostname != NULL)
				my_syslog("bind() on port %s:%d failed: %s", hostname, tcpPort, my_strerror());
			else
				my_syslog("bind() on port %d failed: %s", tcpPort, my_strerror());
			my_exit(1);
		}
		if (listen(s, 5) == (-1)) {
			my_syslog("listen() failed: %s", my_strerror());
			my_exit(1);
		}
		if (hostname != NULL)
			my_syslog("Waiting TCP connection on port %s:%d", hostname, tcpPort);
		else
			my_syslog("Waiting TCP connection on port %d", tcpPort);
		for (;;) {
			sa_rclient_len = sizeof(sa_rclient);
			if ((new_s = accept(s, (struct sockaddr *)&sa_rclient, &sa_rclient_len)) == (-1)) {
				my_syslog("accept() failed: %s", my_strerror());
				h2close = INVALID_HANDLE_VALUE;
				hCom = h2close;
				close(s);
				break;
			}
			if (childcount >= maxtcpclients) {
				my_syslog("Connection from %s:%d refused because maximum number of clients [%d] has been reached", inet_ntoa(sa_rclient.sin_addr), ntohs(sa_rclient.sin_port), maxtcpclients);
				close(new_s);
				h2close = INVALID_HANDLE_VALUE;
				hCom = h2close;
				sleep(2);
				continue;
			} else
				my_syslog("Connection from %s:%d", inet_ntoa(sa_rclient.sin_addr), ntohs(sa_rclient.sin_port));
#ifdef USE_LIBWRAP
			/* using libwrap for controll access */
			if (!auth_libwrap(new_s)) {
				h2close = INVALID_HANDLE_VALUE;
				hCom = h2close;
				close(new_s);
				continue;
			} else
#endif				/* USE_LIBWRAP */
			{
				pid = fork();
				if (pid == 0) {
					/*
					 * now we are the
					 * champions...errr...child ;-)
					 */
					/* close input socket */
					close(s);
					/*
					 * temp. disable handler to revent
					 * closing NULL in signal
					 */
					/* handler                                                 */
					h2close = INVALID_HANDLE_VALUE;
					hCom = new_s;
					break;
				} else if (pid == -1) {
					/* can't fork. system error */
					switch (errno) {
					case EAGAIN:
						{
							/*
							 * resource temp.
							 * unavail. try again
							 * later ;-)
							 */
							my_syslog("fork() failed: %s", my_strerror());
							h2close = INVALID_HANDLE_VALUE;
							hCom = h2close;
							close(new_s);
							close(s);
							my_exit(1);
						};
					case ENOMEM:
						{
							/*
							 * not enough system
							 * memory. hangup?
							 */
							my_syslog("fork() failed: %s", my_strerror());
							h2close = INVALID_HANDLE_VALUE;
							hCom = h2close;
							close(new_s);
							close(s);
							my_exit(1);
						};
					}
				} else {
					/* parent code */
					/* close child socket */
					childcount++;
					h2close = INVALID_HANDLE_VALUE;
					hCom = h2close;
					close(new_s);
					/* continue accepting connection */
					continue;
				}
			}
		}
		if (hCom == INVALID_HANDLE_VALUE) {
			if (hostname != NULL)
				my_syslog("Can't open '%s:%s'", hostname, tcpPort);
			else
				my_syslog("Can't open '%s'", tcpPort);

			hCom = INVALID_HANDLE_VALUE;
			h2close = hCom;
		} else {
			h2close = hCom;
		}

	} else if (is_rtcp) {
		/* connect */
		if ((he_rserver = gethostbyname(rhostname)) == NULL) {
			my_syslog("Invalid hostname to connect: %s", rhostname);
			hCom = INVALID_HANDLE_VALUE;
			my_exit(1);
		}
		if (rtcpPort == 0) {
			my_syslog("Invalid TCP port number to connect: %d", rtcpPort);
			hCom = INVALID_HANDLE_VALUE;
			my_exit(1);
		}
rtcp:
		s = socket(PF_INET, SOCK_STREAM, 0);
		if (s == INVALID_HANDLE_VALUE) {
			my_syslog("socket() failed: %s", my_strerror());
			my_exit(1);
		}
		/* send keepalive packets. if remote end hangs then  */
		/* we'll can know this ?                             */
		if (setsockopt(s, SOL_SOCKET, SO_KEEPALIVE, (char *)&opt, sizeof(opt)) < 0) {
			my_syslog("setsockopt() failed: %s", my_strerror());
			my_exit(1);
		}
		memset(&sa_rclient, 0, sizeof(sa_rclient));
		memcpy(&sa_rclient.sin_addr.s_addr, he_rserver->h_addr_list[0], he_rserver->h_length);
		sa_rclient.sin_family = AF_INET;
		sa_rclient.sin_port = htons(rtcpPort);
		if (connect(s, (struct sockaddr *)&sa_rclient, sizeof(sa_rclient)) < 0) {
			my_syslog("connect() failed: %s", my_strerror());
			hCom = INVALID_HANDLE_VALUE;
			h2close = hCom;
			if (is_rtcp) {
				sleep(next_open_timeout);
				goto rtcp;
			}
			my_exit(1);
		} else
			my_syslog("Connected to %s:%d", inet_ntoa(sa_rclient.sin_addr), ntohs(sa_rclient.sin_port));
		hCom = s;
		h2close = hCom;

	} else
		hCom = open_io(argv[0], speed, data_bits, parity, stop_bits);

	if (hCom == INVALID_HANDLE_VALUE) {
		my_syslog("Can't open '%s', exiting", argv[0]);
		my_exit(1);
	}
	memcpy(dirname + dirlen, filename, strlen(filename));
	if ((cur_logfile = fopen(dirname, "at")) == NULL) {
		my_syslog("Can't open CDR file '%s': %s", dirname, strerror(errno));
		my_exit(1);
	}
	while ((rc = read_block(hCom, buf)) >= 0) {
		if (rc == 0) {
			if ((is_tcp) || (is_rtcp)) {
				/* because we read() in blocking mode so if   */
				/* we've got 0 -> remote peer hangs           */
				if (errno != EINTR) {
					my_syslog("Connection with remote peer %s:%d has been closed, exiting", inet_ntoa(sa_rclient.sin_addr), ntohs(sa_rclient.sin_port));
					h2close = INVALID_HANDLE_VALUE;
					close(hCom);
					if (is_rtcp) {
						my_syslog("Reconnect");
						goto rtcp;
					} else
						my_exit(0);

				}
			} else {
				h2close = INVALID_HANDLE_VALUE;
				close_tty(hCom);
				sleep(next_open_timeout);
				hCom = open_io(argv[0], speed, data_bits, parity, stop_bits);
				if (hCom == INVALID_HANDLE_VALUE) {
					my_syslog("Can't open '%s', exiting", argv[0]);
					my_exit(1);
				}
			}
			continue;
		}
		my_write(buf, cur_logfile, rc);
		my_fflush(cur_logfile);
	}
	my_exit(0);
	return 0;
}

