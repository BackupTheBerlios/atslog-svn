#!/usr/bin/perl
# ATSlog version @version@ build @buildnumber@ www.atslog.dp.ua  
# Copyright (C) 2003 Denis CyxoB www.yamiyam.dp.ua
#                                                       
# Скрипт умеет делать записи в БД. Создан для упрощения процедуры
# создания БД и таблиц.

$config_file="./atslog.conf";

use DBI;
use POSIX qw(locale_h); # Для правильной обработки языковых настроек.

open(IN,$config_file) || die print("Can't open config file $config_file.");
while(<IN>) {
    next if /^#/;
    chomp;
    ($key,$val) = ( /([^=]+)=(.*)/ );
    $key = lc($key);
    $vars{$key} = $val;
}
close(IN);

$langfileprefix="./lang/";
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

#print $vars{msg29}."calls: ";

$host="";
if($vars{sqlhost} ne "localhost"){
    $host = ";host=".$vars{sqlhost};
}

if($vars{sqltype} =~ /PostgreSQL/i){
    if ($ARGV[0] eq ""){
	$sqlrootpassword="";
    }else{
	$sqlrootpassword=$ARGV[0];
    }
    if ($ARGV[1] eq ""){
	$sqluser="pgsql";
    }else{
	$sqluser=$ARGV[1];
    }
    if ( $sqluser eq $vars{sqlmasteruser} ){
	$dbname=$vars{sqldatabase};
    }else{
	$dbname="template1";
    }
    $sqltype="Pg";
}else{
    if ($ARGV[0] eq ""){
	$sqlrootpassword="";
    }else{
	$sqlrootpassword=$ARGV[0];
    }
    if ($ARGV[1] eq ""){
	$sqluser="root";
    }else{
	$sqluser=$ARGV[1];
    }
    $dbname="mysql";
    $sqltype="mysql";
}
$dbh = DBI->connect("dbi:$sqltype:dbname=$dbname$host",$sqluser,$sqlrootpassword,{PrintError=>0});
$rv  = $DBI::err;
if($rv == 1045){
# Выходим с соответствующим кодом возврата в том случае,
# если нет доступа без пароля.
    print STDERR $DBI::errstr,"\n";
    exit 2;
}
if (!$dbh){
    print STDERR $DBI::errstr,"\n";
    exit 3;
}
$toexit=0;
while ($toPrepare = <STDIN>) {
    if($toPrepare ne "" && $toPrepare !~ /^\s*$/ && $toPrepare !~ /^-.*$/ && $toPrepare !~ /^\#.*$/ ){
	@queryChunk=split(";",$toPrepare); 
	if($queryChunk[1] ne ""){
	    $query=$newquery.$queryChunk[0].";";
	    $newquery=$queryChunk[1];
	    $sth = $dbh->prepare($query);
	    if (!$sth->execute){
    		print STDERR $DBI::errstr;
		$toexit=1;
	    }
#	    print "The query is: ".$query."\n";
	}else{
	    $newquery.=$queryChunk[0];
	}
    }
}
$dbh->disconnect;
exit $toexit;
