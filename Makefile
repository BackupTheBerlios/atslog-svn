# ATSlog version @version@ build @buildnumber@ www.atslog.dp.ua  
# Copyright (C) 2003 Denis CyxoB www.yamiyam.dp.ua
#
# Using:
# make all - for building.
#
# make install - for building and installing.
#
# make disableupdate - configure for new installation.
#
# make clean - for cleaning temporaly files.

CC=gcc
MAKE=make
SH=/bin/sh
PREFIX?=/usr/local
PERL?=/usr/bin/perl
CFLAGS = -Wall -O
RM = rm
CDRR_VER_DOT = \"1.09\"
CDRR_VER     = 109



all:
	-if [ ! -r atslog.conf -o ! -r atslogdinit -o ! -r /src/atslogd/atslogd  -o ! -r src/atslogd -o ! -r conf.inc ]; \
	then $(MAKE) config atslogd; \
	fi


atslogd:	src/atslogd/atslogd.c
	-if [ ! -r src/atslogd/atslogd ]; \
	then $(CC) $(CFLAGS) -DCDRR_VER=$(CDRR_VER_DOT) -o src/atslogd/atslogd src/atslogd/atslogd.c; \
	strip src/atslogd/atslogd; \
	fi

config:
	-if [ ! -r atslog.conf -o ! -r atslogdinit -o ! -r conf.inc ]; \
	then ./configure --prefix=${PREFIX} --with-perl=${PERL}; \
	fi

clean:
	-$(RM) -f atslogdinit atslog.conf createsqltables.sql updatesqltables.sql updatesqltables.sql.tmp install.log \
	conf.inc src/atslogd/atslogd include/atslogdb.pl.tmp include/atslogcleardb.pl.tmp

configure:	config
uninstall:	deinstall
remove:		deinstall
clear:	clean
upgrade:	update

install:	all
	$(SH) ./installing --install --sqlroot=${SQLROOT}

disableupdate:	
	$(SH) ./configure --disable-update --prefix=${PREFIX}

deinstall:
	$(SH) ./installing --deinstall
