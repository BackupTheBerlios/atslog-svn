#!/bin/sh
# ATSlog version @version@ build @buildnumber@ www.atslog.dp.ua  
# Copyright (C) 2003 Denis CyxoB www.yamiyam.dp.ua
#                                                       
# FreeBSD start script
PATH=/sbin:/usr/sbin:/bin:/usr/bin:/usr/local/sbin:/usr/local/bin:.

# Readin config file                              
if [ -r /usr/local/etc/atslog.conf ]; then
    . /usr/local/etc/atslog.conf

# Переменная $LANG - берётся из настройки локали.
    if [ -f $sharedir/$langdir/$LANG ]; then
	. $sharedir/$langdir/$LANG
    elif [ -f $sharedir/$langdir/en_US ]; then
	. $sharedir/$langdir/en_US
    else
	echo "Can't open language file"
	exit 1
    fi
else
    echo "Can't open config file"
    exit 1
fi                                                
# Установим рабочие переменные
PATH=$PATH:$bindir:$sharedir

case "$1" in
start)
    $bindir/$masterscript start 1> /dev/null
    if [ "$?" -eq 0 ]
    then
        echo -n " ATSlog"
    else
	echo -n " $msg6"
    fi
    ;;
stop)
    $bindir/$masterscript stop  1> /dev/null
    if [ "$?" -eq 0 ]
    then
	echo -n " ATSlog"
    else
	echo -n " $msg8"
    fi
    ;;
restart)
    $bindir/$masterscript restart
    ;;
status)                  
    $bindir/$masterscript
    ;;                   
*)
  echo "Usage: `basename $0` {start|stop|restart|status}"
    ;;
esac
exit $?
