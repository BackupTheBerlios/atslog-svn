# ATSlog version @version@ build @buildnumber@ www.atslog.dp.ua  
# Copyright (C) 2003 Denis CyxoB www.yamiyam.dp.ua
#
# Using:
# make all - for building.
#
# make install - for building and installing.
#
# make clean - for cleaning temporaly files.

CC=gcc
MAKE=make
SH=/bin/sh
RM= rm
SUBDIR+= src/atslogd
CURDIR?=`pwd`

all:	config atslogd

atslogd:
	@for sub in ${SUBDIR}; do \
	    if test -d ${CURDIR}/$${sub}; then \
		cd ${CURDIR}/$${sub}; \
		${MAKE}; \
	    fi; \
	done

configure:	config

config:
	@if [ ! -r atslog.conf -o ! -r atslogdinit -o ! -r conf.inc ]; \
	then ${SH} ./configure $(CONFIGURE_ARGS); \
	fi

clean:
	@$(RM) -f atslogdinit atslog.conf \
	createsqltables.mysql.sql \
	createuser.pgsql.sql \
	createuser.mysql.sql \
	createsqltables.pgsql.sql \
	updatesqltables.mysql.sql \
	updatesqltables.pgsql.sql \
	updatesqltables.mysql.sql.out \
	updatesqltables.pgsql.sql.out \
	install.log \
	./scripts/createdb.out.pl \
	./scripts/checkDBD.out.pl \
	conf.inc \
	atslogdb.pl atslogcleardb.pl atslogrotate \
	atslogmaster atslogdinit atslogdaily Makefile.out installing.out \
	./src/atslogd/atslogd

configure:	config
uninstall:	deinstall
remove:		deinstall
clear:		clean

install:	all
	@$(SH) ./installing --install --sqlroot=${SQLROOT}

deinstall:
	@$(SH) ./installing --deinstall
