#!/usr/bin/perl
# ATSlog version @version@ build @buildnumber@ www.atslog.dp.ua  
# Copyright (C) 2003 Denis CyxoB www.yamiyam.dp.ua
#                                                       
# Скрипт очищает содержимое базы одним махом
if ($ARGV[0] ne "yes"){
    exit 1;
}

use Mysql;
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

print $vars{msg29}."calls: ";


$dbh = Mysql->Connect($vars{sqlhost}, $vars{sqldatabase}, $vars{sqlmasteruser}, $vars{sqlmaspasswd});
$del_query="DELETE FROM calls;";

#print $del_query;

if ($dbh->Query($del_query)) {
    print $vars{msg17}."\n";
    undef $dbh;
    $toexit=0;
}else{
    undef $dbh;
    print $vars{msg30}."\n";
    $toexit=1;
}
exit $toexit;
