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
RM = rm
SUBDIR += src/atslogd



all:
	@if [ ! -r atslog.conf -o ! -r atslogdinit -o ! -r ./src/atslogd/atslogd -o ! -r conf.inc ]; \
	then $(MAKE) config atslogd; \
	fi

atslogd:
	@for sub in ${SUBDIR}; do \
	    if test -d ${.CURDIR}/$${sub}; then \
		cd ${.CURDIR}/$${sub}; \
		${MAKE}; \
	    fi; \
	done

config:
	@if [ $(PREFIX) ]; \
	then PREFIX?=/usr/local; \
	CONFIGURE_ARGS+= --prefix=${PREFIX}; \
	fi
	@if [ $(PERL) ]; \
	then PERL?=/usr/bin/perl; \
	CONFIGURE_ARGS+= --with-perl=${PERL}; \
	fi
	@if [ $(WITH_POSTGRESQL) ]; \
	then CONFIGURE_ARGS+= --sql-type=PostgreSQL; \
	elif [ $(WITH_MYSQL) ]; \
	then CONFIGURE_ARGS+= --sql-type=MySQL; \
	fi
	@if [ ! -r atslog.conf -o ! -r atslogdinit -o ! -r conf.inc ]; \
	then ./configure $(CONFIGURE_ARGS); \
	fi

clean:
	@$(RM) -f atslogdinit atslog.conf \
	createsqltables.mysql.sql \
	createuser.pgsql.sql \
	createuser.mysql.sql \
	createsqltables.pgsql.sql \
	updatesqltables.mysql.sql \
	updatesqltables.pgsql.sql \
	updatesqltables.mysql.sql.tmp \
	updatesqltables.pgsql.sql.tmp \
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
clear:	clean

install:	all
	@$(SH) ./installing --install --sqlroot=${SQLROOT}

disableupdate: to_disable all
	@echo Disable automatic updating

deinstall:
	@$(SH) ./installing --deinstall

to_disable:
	ifdef $(IGNORE); \
	    CONFIGURE_ARGS+=--disable-update; \
	enif

