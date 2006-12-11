#!/usr/bin/perl
# ATSlog version @version@ build @buildnumber@ www.atslog.dp.ua  
# Copyright (C) 2003 Denis CyxoB www.yamiyam.dp.ua
#                                                       
# Скрипт проверки наличия нужной для работы библиотеки DBD.
#
if (!$ARGV[0]){
    exit 1
}else{
    $sqltype = $ARGV[0];
    if ($sqltype eq "MySQL"){
	$dbiType = "mysql";
    }
    if ($sqltype eq "PostgreSQL"){
	$dbiType = "Pg";
    }
}

use DBI;
$to_exit=2;

@driver_names = DBI->available_drivers;

foreach $failov (@driver_names){
    if ($failov eq $dbiType){
#	print $failov."\n";
        $to_exit = 0;
    }
}
exit $to_exit;
