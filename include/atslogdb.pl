#!/usr/bin/perl
# ATSlog version @version@ build @buildnumber@ www.atslog.dp.ua  
# Copyright (C) 2003 Denis CyxoB www.yamiyam.dp.ua
#

use Sys::Syslog qw(:DEFAULT setlogsock);# Сообщения об ошибках запишем в syslog
use Mysql;# Класс для работы с MySQL
use POSIX qw(locale_h); # Для правильной обработки языковых настроек.


$config_file="/usr/local/etc/atslog.conf";

open(IN,$config_file) || die print("Can't open config file $config_file.");
while(<IN>) {
    next if /^#/;
    chomp;
    ($key,$val) = ( /([^=]+)=(.*)/ );
    $key = lc($key);
    $vars{$key} = $val;
}
close(IN);

$langfileprefix=$vars{sharedir}."/".$vars{langdir}."/";
$langfile=$langfileprefix.setlocale(LC_CTYPE);

if ( ! -f $langfile){
    $langfile=$langfileprefix."en_US";
}

open(LN,$langfile) || die print("Can't open language file $langfile.");
while(<LN>) {
    next if /^#/;
    chomp;
    ($key,$val) = ( /([^=]+)=\"(.*)\"/ );
    $key = lc($key);
    $vars{$key} = $val;
}
close(LN);

# Закоментрированное оставляю для отладки
#$stringNumber=0;
$callsCount=0;
$toexit=0;

# Разберёмся с уровнем для системного журнала
$vars{syslogfacility} =~ /(.*)\.(.*)/;
$sFas1 = $1;
$sFas2 = $2;

# Опишем основные функции

# Функция выводит сообщения об ошибках в системный журнал и в STDERR
sub echoerrors(){
 syslog("$sFas2", "$ERRORMESSAGE");
 warn ("$ERRORMESSAGE\n");
 $toexit=1;
}

sub AmPmTo24(){
    $hour=$_[0];
    $AmPm=$_[1];
    if ($AmPm eq 'PM'){
	if($hour < 12){
	    $return24=$hour+12;
	}elsif($hour == 12){
	    $return24=12;
	}
    }elsif($AmPm eq 'AM'){
        if($hour < 12){
            $return24=$hour;
        }elsif($hour == 12){
            $return24=0;
        }
    }
    return $return24;
}

# Поехали!
setlogsock('unix');#Тип сокета для syslogd
openlog("atslogdb", 'pid, ndelay, cons', "$sFas1");#Откроем сокет на syslogd
if ($dbh = Mysql->Connect($vars{sqlhost}, $vars{sqldatabase}, $vars{sqlmasteruser}, $vars{sqlmaspasswd})){

    if ($vars{model} =~ /SKP-816/i){
        require "$vars{libdir}/skp-816.lib";
    }elsif($vars{model} =~ /KX-TA308RU/i or $vars{model} =~ /KX-TA308/i or $vars{model} =~ /KX-TA616RU/i){
        require "$vars{libdir}/kx-ta616-308-ru.lib";
    }elsif ($vars{model} =~ /KX-TD1232/i){
	require "$vars{libdir}/kx-td1232.lib";
    }elsif ($vars{model} =~ /KX-TD816RU/i){
	require "$vars{libdir}/kx-td816ru.lib";
    }elsif($vars{model} =~ /GD-320/i){
        require "$vars{libdir}/gd-320.lib";
    }elsif($vars{model} =~ /NX-820/i){
        require "$vars{libdir}/nx-820.lib";
    }else{
        $ERRORMESSAGE="$vars{msg31}";
        echoerrors();
	exit $toexit;
    }
    parsecurcalls();

    if($callsCount == 0){                                                           
        $ERRORMESSAGE="$vars{msg32}";
        echoerrors();
    }                                                                       
}else{
 $ERRORMESSAGE="$vars{msg33}"; 
 echoerrors();
}
closelog();
exit $toexit;
