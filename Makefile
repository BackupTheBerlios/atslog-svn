# ATSlog version @version@ build @buildnumber@ www.atslog.dp.ua
# Copyright (C) 2.03 Denis CyxoB www.yamiyam.dp.ua
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

config:	version
	@if [ ! -r atslog.conf -o ! -r atslogdinit -o ! -r conf.inc ]; \
	then ${SH} ${CURDIR}/configure $(CONFIGURE_ARGS); \
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
	${CURDIR}/scripts/createdb.out.pl \
	${CURDIR}/scripts/checkDBD.out.pl \
	conf.inc \
	atslogdb.pl atslogcleardb.pl atslogrotate \
	atslogmaster atslogdinit atslogdaily Makefile.out installing.out \
	atslogdinit.out \
	${CURDIR}/src/atslogd/atslogd

configure:	config
uninstall:	deinstall
remove:		deinstall
clear:		clean

install:	all
	@$(SH) ${CURDIR}/installing --install --sqlroot=${SQLROOT}

deinstall:
	@$(SH) ${CURDIR}/installing --deinstall

version:
	@if [ -r ${CURDIR}/version.inc ]; then \
	    $(SH) ${CURDIR}/scripts/version; \
	    $(RM) ${CURDIR}/version.inc; \
	fi
