#!/usr/bin/perl
# ATSlog version @version@ build @buildnumber@ www.atslog.dp.ua
# Copyright (C) 2003 Denis CyxoB www.yamiyam.dp.ua
#

use Sys::Syslog qw(:DEFAULT setlogsock);# ��������� �� ������� ������� � syslog
use DBI;# ����� ��� ������ � ��
use POSIX qw(locale_h); # ��� ���������� ��������� �������� ��������.


$config_file="/usr/local/etc/atslog.conf";

open(IN,$config_file) || die print("Can't open config file $config_file.\n");
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

open(LN,$langfile) || die print("Can't open language file $langfile.\n");
while(<LN>) {
    next if /^#/;
    chomp;
    ($key,$val) = ( /([^=]+)=\"(.*)\"/ );
    $key = lc($key);
    $vars{$key} = $val;
}
close(LN);

# ������������������ �������� ��� �������
#$stringnumber=0;
$callsCount=0;
$toexit=0;

# �����ң��� � ������� ��� ���������� �������
$vars{syslogfacility} =~ /(.*)\.(.*)/;
$sFas1 = $1;
$sFas2 = $2;

# ������ �������� �������

# ������� ������� ��������� �� ������� � ��������� ������ � � STDERR
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

# �������!
setlogsock('unix');#��� ������ ��� syslogd
openlog("atslogdb", 'pid, ndelay, cons', "$sFas1");#������� ����� �� syslogd
if($vars{sqltype}  =~ /PostgreSQL/i){
    $sqltype=Pg;
}else{
    $sqltype=mysql;
}
$host="";                           
if($vars{sqlhost} ne "localhost"){
    $host = ";host=".$vars{sqlhost};
}

if ($dbh = DBI->connect("dbi:$sqltype:dbname=$vars{sqldatabase}$host",$vars{sqlmasteruser},$vars{sqlmaspasswd},{PrintError=>0})){
    if($vars{sqltype}  =~ /MySQL/i){
        $dbh->{mysql_auto_reconnect} = 1;
    }
    
    
    if($vars{model} =~ /KX-TA308RU/i or $vars{model} =~ /KX-TA308/i or $vars{model} =~ /KX-TA616RU/i){
	$vars{model}="kx-ta616-308-ru";
    }

    $libname="$vars{libdir}/".lc($vars{model}).".lib";

    if ( (-e $libname) && (-r $libname) ) 
    {
	require $libname;
    }else{
        $ERRORMESSAGE="$vars{msg31}";
        echoerrors();
	exit $toexit;
    }
    
    parsecurcalls();

    if($callsCount == 0){                                                           
        $ERRORMESSAGE="\n$vars{msg32}";
        echoerrors();
    }                                                                       
}else{
 $ERRORMESSAGE="$vars{msg33}"; 
 echoerrors();
}
closelog();
$dbh->disconnect;
exit $toexit;
