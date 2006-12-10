#!/usr/bin/perl
# ATSlog www.atslog.dp.ua
# Copyright (C) 2003 Denis CyxoB www.yamiyam.dp.ua
#
# Разбираем строки текстового-лог файла для
# АТС Panasonic KX-TA616RU, Panasonic KX-TA308RU и Panasonic KX-TA308

$sqlhost="localhost";
$sqldatabase="mm";
$sqlmasteruser="mm";
$sqlmaspasswd="mm";
$sqlcallstable="calls";

use Mysql;

$dbh = Mysql->Connect($sqlhost, $sqldatabase, $sqlmasteruser,$sqlmaspasswd);

while ($str=<>)
{
    $stringNumber++;
    if ($str =~ /^.*?(\d+)\/\s??(\d+)\/\s??(\d+)\s*([\**|\s*])\s*(\d+)\:(\d+)(['AM'|'PM']{2})\s+(\d+)\s+(\d+)\s+(.*?)\s+(\d+)\:(\d+)\'(\d+)\".+$/){
        unitecurcalls();
    }else{
	if ($str !~ /^$/){
	    print $str;
	};
    };
};

sub unitecurcalls() {

    $Month = $1;

    $Day=$2;

    $Year=$3+2000;

    $CallHour=&AmPmTo24($5,$7);

    if($4 eq '*'){
	$Forwarded =1;
    }else{
        $Forwarded =0;
    };

    $CallMinute=$6;

    $Internally=$8;

    $CO=$9;

    $forIncoming=$10;

    $Duration = (($11*60*60)+($12*60)+$13);

    $TimeOfCall = "'$Year-$Month-$Day $CallHour\:$CallMinute\:00'";
	
    if($forIncoming =~ /<\s*[[a-z]|\s]*\s*>\s*/i){
	$Way='in';
	$Number=0; # Not Specified
    }else{
	$Way='out';
	$forIncoming =~ s/\D+//;
	$Number = substr($forIncoming,0,100);
    };

    if ($TimeOfCall ne ""){
# Для проверки наличия хоть одной запись звонков подлежащей
# записи в db.
	$callsCount++;
    }
#    print("$stringNumber $TimeOfCall $Forwarded $Internally $CO $Way $Number $Duration\n");
    $ins_query = "INSERT INTO `$sqlcallstable` (`TimeOfCall`, `Forwarded`,`Internally`,`CO`,`Way`,`Number`, `Duration`) VALUES  (".$TimeOfCall.", '$Forwarded', '$Internally', '$CO', '$Way', '$Number', '$Duration');";
    $dbh->Query($ins_query);

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

