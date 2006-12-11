<?php

// Обработаем передаваемые скрипту переменные на тему безопасности и совместимости с
// работой в режиме register_globals
// ------------------------------------------------------------------------------------------------------
//
import_request_variables("gPc", "rvar_");
if (!empty($rvar_day)) $day=translateHtml($rvar_day);
if (!empty($rvar_mon)) $mon=translateHtml($rvar_mon);
if (!empty($rvar_year)) $year=translateHtml($rvar_year);
if (!empty($rvar_day2)) $day2=translateHtml($rvar_day2);
if (!empty($rvar_mon2)) $mon2=translateHtml($rvar_mon2);
if (!empty($rvar_year2)) $year2=translateHtml($rvar_year2);

if (!empty($rvar_cDay)) $cDay=translateHtml($rvar_cDay);
if (!empty($rvar_cMon)) $cMon=translateHtml($rvar_cMon);
if (!empty($rvar_cYear)) $cYear=translateHtml($rvar_cYear);
if (!empty($rvar_cDay2)) $cDay2=translateHtml($rvar_cDay2);
if (!empty($rvar_cMon2)) $cMon2=translateHtml($rvar_cMon2);
if (!empty($rvar_cYear2)) $cYear2=translateHtml($rvar_cYear2);
if (!empty($rvar_cRows)) $cRows=translateHtml($rvar_cRows);

if (!empty($rvar_order)) $order=translateHtml($rvar_order);
if (!empty($rvar_sortBy)) $sortBy=translateHtml($rvar_sortBy);
if (!empty($rvar_type)) $type=translateHtml($rvar_type);
if (!empty($rvar_co)) $co=translateHtml($rvar_co);
if (!empty($rvar_int)) $int=translateHtml($rvar_int);
if (!empty($rvar_toprint)) $toprint=translateHtml($rvar_toprint);
if (!empty($rvar_incoming)) $incoming=translateHtml($rvar_incoming);
if (!empty($rvar_CityLine)) $CityLine=translateHtml($rvar_CityLine);
if (!empty($rvar_MobLine)) $MobLine=translateHtml($rvar_MobLine);
if (!empty($rvar_TrunkLine)) $TrunkLine=translateHtml($rvar_TrunkLine);
if (!empty($rvar_NationalLine)) $NationalLine=translateHtml($rvar_NationalLine);
if (!empty($rvar_cacheflush)) $cacheflush=translateHtml($rvar_cacheflush);
if (isset($rvar_num)) $num=translateHtml($rvar_num);

if (!empty($rvar_debug)) $debug=translateHtml($rvar_debug);
if (!empty($rvar_newStatus)) $newStatus=translateHtml($rvar_newStatus);
if (!empty($rvar_search)) $search=translateHtml($rvar_search);
if (!empty($rvar_rows)) $rows=translateHtml($rvar_rows);
if (!empty($rvar_page)) $page=translateHtml($rvar_page);
if (!empty($rvar_export)) $export=translateHtml($rvar_export); else $export='';
if (!empty($rvar_lang))	$lang=translateHtml($rvar_lang); else $lang='';
if (!empty($rvar_color)) $color=translateHtml($rvar_color);
if (!empty($rvar_baseOrder)) $baseOrder=translateHtml($rvar_baseOrder);
if (!empty($rvar_diatype)) $diatype=translateHtml($rvar_diatype);

// Export
if($export=="excel") {
    include("../include/export/2excel.php");
    $expor_excel = new MID_SQLPARAExel;
}

// Load language
LanguageSetup($lang);

// Colors scheme
ColorSetup($color);

// Соединимся с SQL сервером
connect_to_db();
if (!checkpass()) {
    nopass();
}
if (!hasprivilege("access", false)) {
    nopass();      
}

// Опишем базовые переменные
// ----------------------------------------------------------------------------
//
if(empty($mon))$mon=date("m", mktime (0,0,0,date("m"),1,date("Y")));
if(empty($day))$day=date("d", mktime (0,0,0,date("m"),1,date("Y")));
if(empty($year))$year=date("Y", mktime (0,0,0,date("m"),1,date("Y")));
if(empty($mon2))$mon2=date("m", mktime (0,0,0,date("m")+1,0,date("Y")));
if(empty($day2))$day2=date("d", mktime (0,0,0,date("m")+1,0,date("Y")));
if(empty($year2))$year2=date("Y", mktime (0,0,0,date("m")+1,0,date("Y")));
if(!empty($newStatus)){
 setcookie("cMon","$mon",time() + 60*60,"/",$_SERVER["SERVER_NAME"]);
 setcookie("cDay","$day",time() + 60*60,"/",$_SERVER["SERVER_NAME"]);
 setcookie("cYear","$year",time() + 60*60,"/",$_SERVER["SERVER_NAME"]);
 setcookie("cMon2","$mon2",time() + 60*60,"/",$_SERVER["SERVER_NAME"]);
 setcookie("cDay2","$day2",time() + 60*60,"/",$_SERVER["SERVER_NAME"]);
 setcookie("cYear2","$year2",time() + 60*60,"/",$_SERVER["SERVER_NAME"]);
}else{
 if(!empty($cMon))	$mon = $cMon;
 if(!empty($cDay))	$day = $cDay;
 if(!empty($cYear))	$year = $cYear;
 if(!empty($cMon2))	$mon2 = $cMon2;
 if(!empty($cDay2))	$day2 = $cDay2;
 if(!empty($cYear2))	$year2 = $cYear2;
}

if(empty($type)) $type="IntAll"; elseif(!empty($search)) $type="AllCalls";
if(empty($incoming)) $incoming="2";
if(empty($CityLine) || $CityLine!="1") $CityLine=0;
if(empty($TrunkLine) || $TrunkLine!="2") $TrunkLine=0;
if(empty($MobLine)|| $MobLine!="4") $MobLine=0;
if(empty($NationalLine) || $NationalLine!="8") $NationalLine=0;
if(empty($sortBy)) $sortBy="1";
if(!empty($cRows)) $rows = $cRows;
if(empty($rows)) $rows="100";
if(empty($page)) $page = 0;
if(!empty($baseOrder) && empty($order)) $order=$baseOrder;
if($order!="ASC") $order="DESC";
$additionalReq="";
$hideExcludes=FALSE;
if($CityLine==0 && $TrunkLine==0 && $MobLine==0 && $NationalLine==0) $hideExcludes=TRUE;
//if($cacheflush) $conn->CacheFlush();

// Опишем в массиве те модели АТС, которые имеют АОН.
$withAON = array('KX-TD1232','GD-320','');
$vhodjashij=$GUI_LANG['Incoming'];
$IfAON = $vhodjashij;
while(list ($key, $val) = each ($withAON)){
	if ($val=="$model"){
// Модель АТС описанная переменной $model; взята из конфигурационного файла
		$IfAON = $GUI_LANG['NotRecognized'];
		break;
	}
}


// Вычисление дополнения к SQL запросу в зависимости от направления звонков
// осуществяется посредством вызова математической функции.

$vectorReq=VectorOfCall(MathVector(0));

// Формируем запрос в зависимости от типа звонков: исходящие,
// входящие, или оба типа.
switch($incoming){
	case "1":
		$additionalEcho = $GUI_LANG['IncomingAndOutgoingCalls'];
		break;
	case "2":
		$additionalEcho = $GUI_LANG['OutgoingCalls'];
		$additionalReq.=" AND (calls.way = '2')";
		break;
	case "3":
		$additionalEcho = $GUI_LANG['IncomingCalls'];
		$additionalReq.=" AND (calls.way = '1')";
		break;
}

// Проверим привелегии пользователя
// ----------------------------------------------------------------------------

// Проверим, обображать ли для этого юзера всех абонентов.

if (!hasprivilege("allabonents", false)){
    $additionalReq.=" AND (calls.internally = '".$authrow['internally']."')";
    $noAbonents = 1;
}

if(empty($from_date)) $from_date=sprintf("%04d-%02d-%02d 00:00:00",$year,$mon,$day);
if(empty($to_date)) $to_date=sprintf("%04d-%02d-%02d 23:59:59",$year2,$mon2,$day2); 

?>