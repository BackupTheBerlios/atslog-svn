# Makefile

prefix     = /usr/local
exec_prefix= ${prefix}
LOG_DIR=${prefix}/var/log
CC=gcc
WWW_ROOT=${prefix}/var/www
PIDFILE_DIR=${prefix}/var/run
BIN_DIR=${exec_prefix}/bin
ETC_DIR=${prefix}/etc
LIBEXEC_DIR=${exec_prefix}/libexec
MAN_DIR=${prefix}/man
DATA_DIR=${prefix}/share
RM=/bin/rm
CP=/bin/cp
MKDIR=/bin/mkdir
ECHO=/bin/echo
all: atslog-daemon

atslog-daemon:
	@cd atslogd; $(MAKE)

clean:
	@cd atslogd; $(MAKE) clean

include/atslog.conf:
	sed s/@prefix@/test/g include/atslog.conf.in > /include/atslog.conf

install: all include/atslog.conf
	@${MKDIR} -p ${WWW_ROOT}
	@${ECHO} "Installing web interface to the ${WWW_ROOT}/atslog directory"
	@${CP} -r www/ ${WWW_ROOT}/atslog
	@${ECHO} "Installing atslog libraries to the ${LIBEXEC_DIR}/atslog directory"
	@${CP} -r libexec/ ${LIBEXEC_DIR}/atslog
	@${ECHO} "Installing atslog binaries to the ${BIN_DIR} directory"
	@${CP} atslogd/atslogd include/atslogcleardb.pl include/atslogdaily include/atslogdb.pl include/atslogmaster include/atslogrotate ${BIN_DIR}	
	@${CP} include/atslog.conf.default ${ETC_DIR}
	@if test ! -f ${ETC_DIR}/atslog.conf; then ${CP} ${ETC_DIR}/atslog.conf.default ${ETC_DIR}/atslog.conf;fi
	@${MKDIR} -p ${DATA_DIR}/atslog/lang
	@${MKDIR} -p ${DATA_DIR}/atslog/lang
	@${CP} -r include/lang/* ${DATA_DIR}/atslog/lang
	@${MKDIR} -p ${LOG_DIR}/atslog

deinstall:
	@${RM} -rf ${WWW_ROOT}/atslog
	@${RM} -rf ${LIBEXEC_DIR}/atslog
	@${RM} ${BIN_DIR}/atslogd ${BIN_DIR}/atslogcleardb.pl ${BIN_DIR}/atslogdaily ${BIN_DIR}/atslogdb.pl ${BIN_DIR}/atslogmaster ${BIN_DIR}/atslogrotate
	@if cmp -s ${ETC_DIR}/atslog.conf ${ETC_DIR}/atslog.conf.default; then ${RM} -f ${ETC_DIR}/atslog.conf; fi
	@${RM} ${ETC_DIR}/atslog.conf.default
	@${RM} -rf ${DATA_DIR}/atslog
