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
RM=rm
SUBDIR+= tmp/src/atslogd
CURDIR?=`pwd`
PREFIX?=/usr/local

# Cnfigure args
DISABLELIBWRAP?=NO
DISABLEUPDATE?=NO
SQLTYPE?=MySQL

CONFIGURE_ARGS+= --disable-libwrap=${DISABLELIBWRAP}
CONFIGURE_ARGS+= --disable-update=${DISABLEUPDATE}
CONFIGURE_ARGS+= --sql-type=${SQLTYPE}
CONFIGURE_ARGS+= --prefix=${PREFIX}

all:	version atslogd

atslogd:
	@for sub in ${SUBDIR}; do \
	    if test -d ${CURDIR}/$${sub}; then \
		cd ${CURDIR}/$${sub}; \
		${MAKE}; \
	    fi; \
	done

configure:	config

config:
	@if [ ! -r ${CURDIR}/tmp/configure.flag ]; \
	then ${SH} ${CURDIR}/configure $(CONFIGURE_ARGS); \
	fi

clean:
	@$(RM) -rf ${CURDIR}/tmp

configure:	config
uninstall:	deinstall
remove:		deinstall
clear:		clean

install:	all
	@$(SH) ${CURDIR}/tmp/installing --install --sqlroot=${SQLROOT}

deinstall:
	@$(SH) ${CURDIR}/installing --deinstall

version: config
	@if [ -r ${CURDIR}/tmp/version.inc ]; then \
	    $(SH) ${CURDIR}/scripts/version; \
	    $(RM) ${CURDIR}/tmp/version.inc; \
	    $(RM) ${CURDIR}/tmp/scripts/version; \
	fi
