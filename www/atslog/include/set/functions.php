<?php

 /*

  Опишем общие параметры
  
 */


// Найдём пароль в дебрях переменных от HTTP сервера
// ----------------------------------------------------------------------------
if (empty($PHP_AUTH_PW)) {
    if (!empty($_SERVER) && isset($_SERVER['PHP_AUTH_PW'])) {
	$PHP_AUTH_PW = $_SERVER['PHP_AUTH_PW'];
    }elseif(isset($REMOTE_MD5)) {
	$PHP_AUTH_PW = $REMOTE_MD5;
    }elseif(!empty($_ENV) && isset($_ENV['REMOTE_MD5'])) {
	$PHP_AUTH_PW = $_ENV['REMOTE_MD5'];
    }elseif (@getenv('REMOTE_MD5')) {
	$PHP_AUTH_PW = getenv('REMOTE_MD5');
    }elseif(isset($AUTH_MD5)) {
	$PHP_AUTH_PW = $AUTH_MD5;
    }elseif(!empty($_ENV) && isset($_ENV['AUTH_MD5'])) {
	$PHP_AUTH_PW = $_ENV['AUTH_MD5'];
    }elseif(@getenv('AUTH_MD5')) {
	$PHP_AUTH_PW = getenv('AUTH_MD5');
    }
}
// ----------------------------------------------------------------------------


// Дополнения к SQL запросу в которых описываются типы звонков
// ----------------------------------------------------------------------------
//
$LocalCalls='^[^8]';
$LongDistanceCalls='^8.+';
$InternationalCalls='^810.+';
// Long distance codes of mobile operators.
// Международные коды мобильных операторов.
$MobileCallsR='^8050.+|^8066.+|^8067.+|^8068.+|^8097.+|^8039.+|^8910.+|^8916.+|^8917.+|^8903.+|^8905.+|^8926.+|^8901.+';
/*

UMC Ukraine - 050
JEANS Ukraine - 066
Kyivstar Ukraine - 067
DJUICE Ukraine - 068, 097
GolgenTelecom Ukaraine - 039
Life:) Ukraine - 063
МТС Russia - 910, 916, 917
БиЛайн Russia - 903, 905
Мегафон Russia - 926
МСС Russia - 901

*/



// ----------------------------------------------------------------------------
/*

 Опишем синтаксис регуляного выражения для разных SQL серверов

*/

if($sqltype == 'PostgreSQL'){
    $REGEXP = "~*";
    $NOT_REGEXP = "!~*";
}else{
    $REGEXP = "RLIKE";
    $NOT_REGEXP = "NOT RLIKE";
}


// ----------------------------------------------------------------------------

 /*

   Соединяемся c SQL посредством ADODB

 */
import_request_variables("gPc","rvar_");
if(!empty($rvar_debug)) $debug=translateHtml($rvar_debug); else $debug=0;
include('../include/adodb/adodb.inc.php'); // load code common to ADOdb
// Создадим каталог для кеширования запросов.
if(!file_exists($ADODB_CACHE_DIR)) mkdir($ADODB_CACHE_DIR, 0777);
if($sqltype == "PostgreSQL"){
    $adodbDriver="postgres7";
    if($sqlhost == "localhost") $sqlhost = "";
}else{
    $adodbDriver="MySQL";
}
$conn = &ADONewConnection($adodbDriver);   // create a connection 
$conn->cacheSecs = $ADODB_CACHE_TTL;   // время жизни кеша SQL запроса
$conn->PConnect($sqlhost,$sqlmasteruser,$sqlmaspasswd,$sqldatabase); // connect to SQL, agora db
if($debug==2) $conn->debug = true;


 /*
   Опишем функции
 */

 /*
 
  Математическая составляющая для VectorOfCall.
 
 */
 
function MathVector($delta){
// $delta - Дополнительное сочетание исключений.

    global $CityLine,$TrunkLine,$MobLine,$NationalLine;
    $z=0; $y=0; $x=0; $w=0; $vect_summ=0;
    if (isset($debug) && $debug) $f_delta=$delta;

    $c=($delta > 0) ? $delta/8 : 0;
    if($c >= 1) {$delta-=8;}
    if ($NationalLine > 0 or $c >= 1) {$z=8;$vect_summ+=$z;}

    $c=($delta > 0) ? $delta/4 : 0;
    if($c >= 1) {$delta-=4;}
    if ($MobLine > 0 or $c >= 1) {$y=4;$vect_summ+=$y;}

    $c=($delta > 0) ? $delta/2 : 0;
    if($c >= 1) {$delta-=2;}
    if ($TrunkLine > 0 or $c >= 1) {$x=2;$vect_summ+=$x;}

    $c=$delta-1;
    if($c >= 0) {$delta-=1;}
    if ($CityLine > 0 or $c >= 0) {$w=1;$vect_summ+=$w;}

    if (isset($debug) && $debug) echo "$f_delta $vect_summ<BR>";
    return $vect_summ;;
}


 /*
 
  Выясним часть SQL запроса зависящего от направления звонка.
 
 */
function VectorOfCall($vector){
    global $REGEXP,$NOT_REGEXP;
    global $LocalCalls,$LongDistanceCalls,$MobileCallsR,$InternationalCalls;

    // Все возможные варианты дополнений к SQL запросу в зависимости от направления звонков.
    // 1  - Исключить городские
    $result[1]=" AND ((calls.number $REGEXP '".$LongDistanceCalls."') OR (calls.number $REGEXP '".$MobileCallsR."' OR calls.number $REGEXP '".$InternationalCalls."'))";
    // 2  - Исключить межгород
    $result[2]=" AND ((calls.number $REGEXP '".$LocalCalls."') OR (calls.number $REGEXP '".$MobileCallsR."') OR (calls.number $REGEXP '".$InternationalCalls."'))";
    // 3  - Исключить городские и межгород
    $result[3]=" AND ((calls.number $REGEXP '".$MobileCallsR."' OR calls.number $REGEXP '".$InternationalCalls."'))";
    // 4  - Исключить сотовую связь
    $result[4]=" AND (calls.number $NOT_REGEXP '".$MobileCallsR."')";
    // 5  - Исключить сотовую связь и городские
    $result[5]=" AND ((calls.number $REGEXP '".$LongDistanceCalls."') OR (calls.number $REGEXP '".$InternationalCalls."'))";
    // 6  - Исключить межгород и сотовую связь
    $result[6]=" AND ((calls.number $REGEXP '".$LocalCalls."' OR calls.number $REGEXP '".$InternationalCalls."'))";
    // 7  - Исключить городские, межгород и сотовую
    $result[7]=" AND (calls.number $REGEXP '".$InternationalCalls."')";
    // 8  - Исключить международку
    $result[8]=" AND (calls.number $NOT_REGEXP '".$InternationalCalls."')";
    // 9  - Исключить городские и международку
    $result[9]=" AND ((calls.number $NOT_REGEXP '".$InternationalCalls."' AND (calls.number $REGEXP '".$LongDistanceCalls."' OR calls.number $REGEXP '".$MobileCallsR."')))";
    // 10 - Исключить межгород и международку
    $result[10]=" AND ((calls.number $REGEXP '".$LocalCalls."' OR calls.number $REGEXP '".$MobileCallsR."'))";
    // 11 - Исключить городские, межгород и международку
    $result[11]=" AND (calls.number $REGEXP '".$MobileCallsR."')";
    // 12 - Исключить сотовую и международку
    $result[12]=" AND ((calls.number $NOT_REGEXP '".$MobileCallsR."' AND calls.number $NOT_REGEXP '".$InternationalCalls."'))";
    // 13 - Исключить городские, сотовую и международку
    $result[13]=" AND ((calls.number $REGEXP '".$LongDistanceCalls."' AND calls.number $NOT_REGEXP '".$MobileCallsR."' AND calls.number $NOT_REGEXP '".$InternationalCalls."'))";
    // 14 - Исключить межгород, сотовую и международку
    $result[14]=" AND (calls.number $REGEXP '".$LocalCalls."')";
    // 15 - Исключить все: город, межгород, сотовая и международку
    $result[15]=" AND 0 > 1";
    if(isset($result[$vector])) return($result[$vector]);
}

//  Language file
// ---------------------------------------------------------------------------- 
function LanguageSetup($lang){
    global $GUI_LANG;

    if(!empty($lang)){
	include("../include/lang/".$lang.".php");
    }else{
	if(ereg("ru",$_SERVER['HTTP_ACCEPT_LANGUAGE'])){                                
	    include("../include/lang/ru_1251.php");                                             
	}else{                                                                          
	    include("../include/lang/en_US.php");                                               
	}                                                                               
    }
}

//  Color scheme
// ---------------------------------------------------------------------------- 
function ColorSetup($color){
    global $COLORS,$skin_name;

    $file = "../include/colors/".$color.".php";
    $classic_file = "../include/colors/classic.php";

    if(file_exists($classic_file)) {
	include($classic_file);
	$skin_name = "classic";
    }

    if(!empty($color)){
	if(file_exists($file)) {
	    include($file);
	    $skin_name = $color;
	}
    }
}

function microtime_diff($a, $b) {
 /*
   Функция замера потраченного на формирование страницы времени
 */

   list($a_dec, $a_sec) = explode(" ", $a);
   list($b_dec, $b_sec) = explode(" ", $b);
   return (($b_sec - $a_sec) + ($b_dec - $a_dec));
}                                                 

function print_select_mon($selname)
{
	global $GUI_LANG;
	
	$Month[$selname]=" SELECTED";
	for($i=1;$i<=12;$i++){
	    if($i < 10){ $m_num="0".$i;}else{$m_num="$i";}
	    echo ("<option value=\"$m_num\"".(isset($Month[$m_num])?$Month[$m_num]:'').">".$GUI_LANG['Month'.$m_num]."</option>\n");
	}
};

function sumTotal($seconds,$type){
    global $GUI_LANG;

 /* Функция получает секунды, а возвращает массив с количеством
 	часов, минут и секунд содержащихся в исходных данных.
 */
    if($seconds!=0){
	$hours = ($seconds/(60*60));
	$altogether[0] = sprintf ("%d",$hours);
	$altogether[1] = gmdate("i",$seconds);
	$altogether[2] = gmdate("s",$seconds);
	$toreturn=sprintf("%s %s, %s %s, %s %s",$altogether[0],$GUI_LANG['Hours'],$altogether[1],$GUI_LANG['Minutes'],$altogether[2],$GUI_LANG['Seconds']);
	if($type==1){
	    return ("<b>".$toreturn."</b>");
	}elseif($type==2){
	    return ($toreturn);
	}else{
	    $toreturn=sprintf("%s:%s:%s",$altogether[0],$altogether[1],$altogether[2]);
	    return ($toreturn);
	}
    }else{
	if($type==1){
	    return ("<b>0</b>");
	}else{
	    return ("0");
	}
    }
}

function nopass($wait=FALSE){

    if($wait){
	echo("
<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">
<HTML><HEAD>                                        
<TITLE>Access deny</TITLE>           
</HEAD><BODY>                                       
<H1>Access deny</H1>
<P>Access deny. Go in five minutes.<P>
</BODY></HTML>");

	exit;

    }else{
	header('WWW-Authenticate: Basic realm="abonent"');
	header('HTTP/1.1 401 Unauthorized');
        echo("                                                
<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">  
<HTML><HEAD>                                          
<TITLE>401 Authorization Required</TITLE>             
</HEAD><BODY>                                         
<H1>Authorization Required</H1>                       
<P>This server could not verify that you                 
are authorized to access the document                 
requested.  Either you supplied the wrong             
credentials (e.g., bad password), or your             
browser doesn't understand how to supply              
the credentials required.<P>
</BODY></HTML>");

	die;

    }
}

function connect_to_db()
{
	global $authrow,$sqlhost,$sqlmasteruser,$sqlmaspasswd,$sqldatabase,$conn;
	if(isset($_SERVER['PHP_AUTH_USER'])){
		$q2="select internally, login, lastname, firstname, secondname from users where login ='".$_SERVER['PHP_AUTH_USER']."'";
		$res=$conn->getRow($q2);
		$authrow=$res;
	}
}

//
// Проверим привелегии пользователя и выдадим ему ссылки
// ----------------------------------------------------------------------------
function menucomplit($from){

    global $local_cacheflush,$local_order,$local_sortBy,$order,$sortBy,$GUI_LANG,
    $local_page,$page,$local_search,$search,$local_export,$local_diatype,$diatype;
    global $type;

    echo "<td valign=top>";

    echo "<a href=\"".complitLink($local_cacheflush="1",$local_order=$order,$local_sortBy=$sortBy,$local_page=$page,$local_search=$search,$local_diatype=$diatype)."\" title=\"".$GUI_LANG['ToClearTheCacheAndRefreshThePage']."\">".$GUI_LANG['Refresh']."</a><br>";

    if ($from != "settings"){
	echo("<a href=\"../settings/\" title=\"".$GUI_LANG['UsersSettings']."\">".$GUI_LANG['Settings']."</a><br>");
    }

    if ($from != "phonebook"){
	echo("<a href=\"../phonebook/\" title=\"".$GUI_LANG['PhoneBook']."\">".$GUI_LANG['PhoneBook']."</a><br>");
    }


    echo"<a href=\"../calls/\" title='".$GUI_LANG['StartAllOverAgain']."'>".$GUI_LANG['StartAllOverAgain']."</a><br>";

    if (hasprivilege("parameters", false) && $from != "intern"){
	echo("<a href='../intern/' title='".$GUI_LANG['ParametersOfInternalPhones']."'>".$GUI_LANG['ParametersOfInternalPhones']."</a><br>");
    }
    if (hasprivilege("usersadmin", false) && $from != "users"){
	echo "<a href=\"../users/\" title=\"".$GUI_LANG['ManagementOfAbonents']."\">".$GUI_LANG['ManagementOfAbonents']."</a><br>";
    }
    if (hasprivilege("parameters", false) && $from != "lines"){
	echo("<a href='../lines/' title='".$GUI_LANG['ParametersOfLines']."'>".$GUI_LANG['ParametersOfLines']."</a><br>");
    }

    if ($from != "calls"){
	echo("<a href=\"../calls/\" title=\"".$GUI_LANG['TheAccountOfPhoneCalls']."\">".$GUI_LANG['TheAccountOfPhoneCalls']."</a><br>");
    }
    if ($from == "calls" && $type!="diagram" or $from == "phonebook"){
	echo("<a href=\"".complitLink($local_export="excel",$local_order=$order,$local_sortBy=$sortBy,$local_page=$page,$local_search=$search)."\" title=\"".$GUI_LANG['ExportDataInExcelFormat']."\">".$GUI_LANG['ExportInExcel']."</a><br>");
    }
    echo "</td>";
}

function hasprivilege($priv, $redirect = false, $username = false)
{
	if(!$username){
	    $username = $_SERVER['PHP_AUTH_USER'];
	}

	global $authrow,$conn;
	$q1="select 0 from usersgroups where login = '".$username."' and groups = '$priv'";
	$res = $conn->Execute($q1);
	$toReturn=FALSE;
	if (isset($res->fields[0])){
	    $toReturn=TRUE;
	}
	if ($redirect)
	{
		if (!$toReturn)
		{
			header("Location: ../calls/");
			die;
		}
	}
	else
	return $toReturn;
}

function checkpass()
{
	global $conn,$PHP_AUTH_PW;
	if(isset($_SERVER["PHP_AUTH_USER"])){
		$q3="select 0 from users where login = '".$_SERVER['PHP_AUTH_USER']."' and password = MD5('".$PHP_AUTH_PW."')";
		$res = $conn->Execute($q3);
		$toReturn=FALSE;
		if (isset($res->fields[0])){
			$qD="DELETE FROM unauth WHERE ip = '".$_SERVER['REMOTE_ADDR']."'";
			$conn->Execute($qD);
			$toReturn=TRUE;
		}else{
    	    $qA="SELECT count(username) as falses FROM unauth WHERE logintime > DATE_SUB(NOW(),INTERVAL 5 MINUTE) AND ip = '".$_SERVER['REMOTE_ADDR']."'";
			$resA = $conn->Execute($qA);
    	    if ($resA->fields[0] > 5) {
				nopass(TRUE);
				die();
			};
			
			$qC="INSERT INTO unauth (username, pass, ip,x_forwardeded_for) VALUES ('".$_SERVER['PHP_AUTH_USER']."', '".$PHP_AUTH_PW."','".$_SERVER['REMOTE_ADDR']."', '".(isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:'')."')";
			$conn->Execute($qC);
			//sleep(2);
		}
		return $toReturn;
	}
}

function IsDefaultPass($user)
{
	global $conn,$demoMode;

	$toReturn=FALSE;
	if($user=="atslog" && !$demoMode){
	    $q1="select 0 from users where login = '".$user."' and password = MD5('atslog')";
	    $res = $conn->Execute($q1);
	    if (isset($res->fields[0])){
		$toReturn=TRUE;
	    }
	}
	return $toReturn;
}

function Permissions(){
    global $privileges,$GUI_LANG;

    $privileges = array(
	$GUI_LANG['TheAbonentIsActivated'], "access",
	$GUI_LANG['ManagementOfAbonents'], "usersadmin",
	$GUI_LANG['FullStatistics'], "allabonents",
	$GUI_LANG['Parameters'], "parameters"
    );
}

function showprivileges($login, $readonly)
{
    global $privileges;
    for ($i = 0; $i < count($privileges); $i+=2)
    {
	if(hasprivilege($privileges[$i+1],false, $login) && isset($login)){
	    if($readonly){
		echo ($i ? "<BR>" : "<TD>").$privileges[$i];
	    }else{
		echo ($i ? "<BR>" : "<TD>")."<INPUT TYPE=\"checkbox\" NAME=\"priv".($i/2)."\" checked".">".$privileges[$i];
	    }
	}else{
	    if($readonly){
		echo ($i ? "<BR>" : "<TD>");
	    }else{
		echo ($i ? "<BR>" : "<TD>")."<INPUT TYPE=\"checkbox\" NAME=\"priv".($i/2)."\">".$privileges[$i];
	    }
	}
    }
}

function setprivileges($login)
{
    global $privileges,$conn,$demoMode;
    for ($i = 0; $i < count($privileges); $i+=2){
	if (isset($_POST["priv".($i/2)]) && $_POST["priv".($i/2)]){
	    if (isset($debug) && $debug) echo "priv".($i/2)." = ".$_POST["priv".($i/2)]."<BR>";
	    if(!isset($demoMode) || !$demoMode){
		$q4="insert into usersgroups (login, groups) values ('$login', '".$privileges[$i+1]."')";
		$conn->Execute($q4);
	    }
	}
    }
}

function pechat_ishodnyh()
{
    global $from_date,$to_date,$int_echo,$incoming;
    global $additionalEcho,$coLineDescription,$co,$int,$num;
    global $toprint,$CityLine,$MobLine,$NationalLine,$TrunkLine;
    global $intPhoneDescription,$vhodjashij,$GUI_LANG;
    global $NumberDescription;
    

    echo("<h3>".strtr($GUI_LANG['TypeOfReport'],$GUI_LANG['UpperCase'],$GUI_LANG['LowerCase']).": $int_echo<br>");
    if(!empty($incoming)) echo ("$additionalEcho<br>");
    if(empty($coLineDescription)) $coLineDescription=$co;
	else $coLineDescription="$co ($coLineDescription)";
    if(!empty($co)) echo ($GUI_LANG['ThroughAnExternalLine'].": $coLineDescription<br>");
    if(!empty($int)){
	if(empty($intPhoneDescription)) {
	    $intPhoneDescription=$int;
	}else{
	    $intPhoneDescription="$int ($intPhoneDescription)";
	}

	echo ($GUI_LANG['FromInternalPhone'].": $intPhoneDescription<br>");
    }
    if($num!=''){
	if($num == "0"){
	    $numEcho=$vhodjashij;
	}else{
	    $numEcho=$num;
	}
	if(!empty($NumberDescription)) $numEcho = $numEcho." (${NumberDescription})";
	echo (strtr($GUI_LANG['Number'],$GUI_LANG['UpperCase'],$GUI_LANG['LowerCase']).": $numEcho<br>");
    }
    if ($toprint=="yes") echo (strtr($GUI_LANG['PeriodFrom'],$GUI_LANG['UpperCase'],$GUI_LANG['LowerCase']).": $from_date ".$GUI_LANG['To'].": $to_date<br>");
    if ($toprint=="yes" && $TrunkLine) echo $GUI_LANG['AreExcludedTrunk']."<br>";
    if ($toprint=="yes" && $CityLine) echo $GUI_LANG['AreExcludedCity']."<br>";
    if ($toprint=="yes" && $MobLine) echo $GUI_LANG['AreExcludedCellularCommunication']."<br>";
    if ($toprint=="yes" && $NationalLine) echo $GUI_LANG['AreExcludedLongDistance']."<br>";
    echo ("</h3>");
}

function AddTableHeader($fname,$thname,$toprint){
  global $sortBy, $order,$local_order,$local_sortBy;
  global $GUI_LANG,$local_search,$search;
  global $baseOrder;
	if ($toprint!="yes"){
		if ($sortBy==$fname){
			if ($order=="DESC"){
			    $now_order="ASC";
			    $now_title=$GUI_LANG['SortByIncrease'];
			    $now_img="&nbsp;&nbsp;<img alt=\"".$GUI_LANG['SortByIncrease']."\" src=\"../include/img/arrowAsc.gif\" width=7 height=7 border=0>";
			}else{
			    $now_order="DESC";
			    $now_title=$GUI_LANG['SortByDecrease'];
			    $now_img="&nbsp;&nbsp;<img alt=\"".$GUI_LANG['SortByDecrease']."\" src=\"../include/img/arrowDesc.gif\" width=7 height=7 border=0>";
			}
		}else{
		    if ($order=="DESC"){
			$now_order="DESC";
			$now_title=$GUI_LANG['SortByDecrease'];
		    }else{
			$now_order="ASC";
			$now_title=$GUI_LANG['SortByIncrease'];
		    }
		}
		if(!isset($now_img)) $now_img='';
		echo (" <a href=\"".complitLink($local_order=$now_order,$local_sortBy="$fname",$local_search=$search)."\" title=\"".$now_title."\">$thname</a>".$now_img);
	}else{
		echo ($thname);
	}
}

function totalTableFooter($field,$returnType){

	global $conn,$from_date,$to_date,$additionalReq,$LocalCalls;
	global $isNoCityCalls,$LongDistanceCalls,$MobileCallsR;
	global $InternationalCalls,$debug,$cacheflush,$GUI_LANG;
	global $REGEXP,$NOT_REGEXP;
	global $TTFfirst;
	
	switch($field){
	    case "4":
		// Всего: общее количество звонков
		$q="SELECT COUNT(*)
		from calls
		where
		((calls.timeofcall>='".$from_date."') AND (calls.timeofcall<='".$to_date."')
		".$additionalReq."
		".VectorOfCall(MathVector(0))."
		)";
		break;
	    case "5":
		// Всего: городские
		$q="SELECT SUM(calls.duration),COUNT(*)
		from calls
		where
		((calls.timeofcall>='".$from_date."') AND (calls.timeofcall<='".$to_date."')
		".$additionalReq."
		".VectorOfCall(MathVector(14))."
		)";
		$Qcomment=$GUI_LANG['City'];
		break;
	    case "6":
		// Всего: межгород
		$q="SELECT SUM(calls.duration),COUNT(*)
		from calls
		where
		((calls.timeofcall>='".$from_date."') AND (calls.timeofcall<='".$to_date."')
		".$additionalReq."
		".VectorOfCall(MathVector(13))."
		)";
		$Qcomment=$GUI_LANG['Trunk'];
		break;
	    case "7":
		// Всего:  мобильная связь
		$q="SELECT SUM(calls.duration),COUNT(*)
		from calls
		where ((calls.timeofcall>='".$from_date."') AND (calls.timeofcall<='".$to_date."')
		".$additionalReq."
		".VectorOfCall(MathVector(11))."
		)";
		$Qcomment=$GUI_LANG['Mobile'];
		break;
	    case "8":
		// Всего: международная связь
		$q="SELECT SUM(calls.duration),COUNT(*)
		from calls
		where ((calls.timeofcall>='".$from_date."') AND (calls.timeofcall<='".$to_date."')
		".$additionalReq."
		".VectorOfCall(MathVector(7))."
		)";
		$Qcomment=$GUI_LANG['LongDistance'];
		break;
	}
	if($cacheflush) $res = $conn->CacheFlush($q);
	$local_cacheflush="";
	$res = $conn->CacheExecute($q);

	if ($debug)  echo "<hr>".$q."<br><hr>";

	if($returnType==1){
	    $results=$res->fields[0];
	    if(!$TTFfirst) $TTFfirst=TRUE;
	}else{
	    $total=$res->fields;

	    if($total[0]){
		if($returnType==0 && $TTFfirst) $results="<BR>\n";
		$results.="$Qcomment: ";
		if($returnType==0){
		    $results.=sumTotal($total[0],1);
		}else{
		    $results.=sumTotal($total[0],2);
		}
    		if($total[1]) $results.=". ".$GUI_LANG['OfCalls'].": ";
		if($returnType==0){
		    $results.="<b>";
		}
		$results.= $total[1];
		if($returnType==0){
		    $results.="</b>\n";
		}
		
		if(!$TTFfirst) $TTFfirst=TRUE;
	    }
	}
	
	return($results);

}


function translateHtml($content)
{
	$content = preg_replace("/[^\w_\-]/", "",$content);
	$content = ereg_replace("\\;","&#59",$content);
	$content = htmlentities($content,ENT_QUOTES);
	$content = ereg_replace("\(","&#040",$content);
	$content = ereg_replace("\)","&#041",$content);
	$content = ereg_replace("\\$","&#036",$content);
	$content = stripslashes($content);
	return $content;
}

// Функция выводит сформированую ссылку в зависимости от переданных параметров
function complitLink(){

    global $day,$mon,$year,$day2,$mon2,$year2,
    $sortBy,$type,$co,$int,$toprint,$incoming,
    $CityLine,$TrunkLine,$MobLine,$NationalLine,$debug,$debugMode,
    $num,$rows;

    global $local_day,$local_mon,$local_year,$local_day2,
    $local_mon2,$local_year2,$local_order,
    $local_sortBy,$local_type,$local_co,$local_int,$local_toprint,
    $local_incoming,$local_CityLine,$local_TrunkLine,$local_MobLine,
    $local_NationalLine,$local_debug,$local_cacheflush,
    $local_num,$local_page,$local_search,$local_export,$local_diatype;
	
	if(empty($cacheflush))  $cacheflush='';
	if(empty($NationalLine))  $NationalLine='';
	$complitLine='';
	
    $cL['day'] = (empty($local_day)) ? $day : $local_day;
    $cL['mon'] = (empty($local_mon)) ? $mon : $local_mon;
    $cL['year'] = (empty($local_year)) ? $year : $local_year;
    $cL['day2'] = (empty($local_day2)) ? $day2 : $local_day2;
    $cL['mon2'] = (empty($local_mon2)) ? $mon2 : $local_mon2;
    $cL['year2'] = (empty($local_year2)) ? $year2 : $local_year2;
    if(!empty($local_order)) $cL['order'] = $local_order;
    if(!empty($local_sortBy)) $cL['sortBy']= $local_sortBy;
    $cL['type']= (empty($local_type)) ? $type : $local_type;
    $cL['co'] = (empty($local_co)) ? $co : $local_co;
    $cL['int'] = (empty($local_int)) ? $int : $local_int;
    $cL['toprint'] = (empty($local_toprint)) ? $toprint : $local_toprint;
    $cL['incoming']= (empty($local_incoming)) ? $incoming : $local_incoming;
    $cL['CityLine']= (empty($local_CityLine)) ? $CityLine : $local_CityLine;
    $cL['TrunkLine']= (empty($local_TrunkLine)) ? $TrunkLine : $local_TrunkLine;
    $cL['MobLine']= (empty($local_MobLine))	? $MobLine : $local_MobLine;
    $cL['NationalLine'] = (empty($local_NationalLine)) ? $NationalLine : $local_NationalLine;
    $cL['num'] = ($local_num == "") ? "$num" : "$local_num";
    $cL['cacheflush'] = (empty($local_cacheflush)) ? $cacheflush : $local_cacheflush;
    if(!empty($local_page)) $cL['page'] = $local_page;
    if(!empty($rows)) $cL['rows'] = $rows;
    if(!empty($local_search)) $cL['search'] = $local_search;
    if(!empty($local_export)) $cL['export'] = $local_export;
    if(!empty($local_diatype)) $cL['diatype']= $local_diatype;

    if($debugMode){
	$cL['debug'] = (empty($local_debug)) ? $debug : $local_debug;
    }

    $LocalFlag=1;
    while (list($k, $v) = each($cL)) {
	if("$v" != ""){
	    if($LocalFlag == 1){
		$complitLine .= "?$k=$v";
		$LocalFlag++;
	    }else{
		$complitLine .= "&$k=$v";
	    }
	}
    }

    $local_day="";
    $local_mon="";
    $local_year="";
    $local_day2="";
    $local_mon2="";
    $local_year2="";
    $local_order="";
    $local_sortBy="";
    $local_type="";
    $local_co="";
    $local_int="";
    $local_toprint="";
    $local_incoming="";
    $local_CityLine="";
    $local_TrunkLine="";
    $local_MobLine="";
    $local_NationalLine="";
    $local_debug="";
    $local_cacheflush="";
    $local_num="";
    $local_page="";
    $local_export="";
    $local_diatype="";

    return $complitLine;


}


function pagesNavigator($pages,$page){
    global $local_page,$local_search,$GUI_LANG,
    $toprint,$local_order,$order,$local_sortBy,$sortBy,$search,$skin_name;
?>
<table cellpadding=1 cellspacing=0 border=0 align=center>
	<tr>
<?php
// Печатаем навигатор по страницам

if ($pages>1 && !$toprint){
	if ($page>0){
		print("<td><a href=\"".complitLink($local_page="0",$local_order=$order,$local_sortBy=$sortBy,$local_search=$search)."\" title=\"".$GUI_LANG['FirstPage']." (1)\"><img border=0 width=11 height=10 src=\"../include/img/colors/".$skin_name."/arrows/pages_left.gif\" alt=\"".$GUI_LANG['FirstPage']."\"></a>&nbsp;</td>");
	}

	if ($page>1){
		$prevGoPage = $page-1;
		print("<td><a href=\"".complitLink($local_page="$prevGoPage",$local_order=$order,$local_sortBy=$sortBy,$local_search=$search)."\" title=\"".$GUI_LANG['PreviousPage']." ($page)\"><img border=0 width=12 height=10 src=\"../include/img/colors/".$skin_name."/arrows/next_left.gif\" alt=\"".$GUI_LANG['PreviousPage']."\"></a>&nbsp;</td>");
	}

	print("
		<td>".$GUI_LANG['Pages'].":</td>
	");

	if ($page<($pages-2)){
		$nextGoPage = $page+1;
		print("<td>&nbsp;<a href=\"".complitLink($local_page="$nextGoPage",$local_order=$order,$local_sortBy=$sortBy,$local_search=$search)."\" title=\"".$GUI_LANG['NextPage']." (".($nextGoPage+1).")\"><img border=0 width=12 height=10 src=\"../include/img/colors/".$skin_name."/arrows/next_right.gif\" alt=\"".$GUI_LANG['NextPage']."\"></a></td>");
	}

	if ($page<($pages-1)){
		$nextGoPage = $pages-1;
		print("<td>&nbsp;<a href=\"".complitLink($local_page="$nextGoPage",$local_order=$order,$local_sortBy=$sortBy,$local_search=$search)."\" title=\"".$GUI_LANG['LastPage']." ($pages)\"><img border=0 width=11 height=10 src=\"../include/img/colors/".$skin_name."/arrows/pages_right.gif\" alt=\"".$GUI_LANG['LastPage']."\"></td>");
	}

?>
	</tr>
</table>
<br>
<table cellpadding=2 cellspacing=0 border=0 align=center>
	<tr><td>
<?php
// Печатаем список страниц

    $startList = 1;
    $stopList = 1;
    $numbers = 10; //  Количество печатаемых одновременно страниц.
    if($pages>1){
	$startList=(floor($page/$numbers)*$numbers)+1;
	$stopList=$startList+$numbers-1;
	if ($startList > 1){
	    $prevList = $startList-2;
	    print("<a href=\"".complitLink($local_page="$prevList",$local_order=$order,$local_sortBy=$sortBy,$local_search=$search)."\" title=\"".$GUI_LANG['PreviousListOfPages']."\"><img border=0 width=10 height=10 src=../include/img/colors/".$skin_name."/arrows/end_left.gif> ...</a> ");
	}

	$Num=$startList-1;
	for($zu=$startList;$zu<=$stopList;$zu++){
	    if( $zu <= $pages && 1 <= $zu){
		if($zu == ($page+1)){
		    echo (" <font size=+1>$zu</font> ");
		}else{
		    echo (" <a href=\"".complitLink($local_page="$Num",$local_order=$order,$local_sortBy=$sortBy,$local_search=$search)."\" title=\"".$GUI_LANG['Page']." $zu\">$zu</a> ");
		}
	    }
	    $Num++;
	}

	if ($stopList < $pages){
		$nextList = $stopList;
		print("<a href=\"".complitLink($local_page="$nextList",$local_order=$order,$local_sortBy=$sortBy,$local_search=$search)."\" title=\"".$GUI_LANG['NextListOfPages']."\">... <img border=0 width=10 height=10 src=../include/img/colors/".$skin_name."/arrows/end_right.gif></a>");
	}
    }
}

//echo("</td></tr><tr><td>pages = $pages, page = $page, startLine = $startLine, lastLine = $lastLine, startList = $startList, stopList = $stopList");

?>
</td></tr>
</table>

<?php
}

// Функция возвращает строку с LIMIT.
// Нужно для разбиения на страницы.
// ----------------------------------------------------------------------------
function getLimits($qP){
    global $conn,$debug,$cacheflush,$pages,$page,$rows;
    global $sqltype;

    if($debug) echo "// Information for LIMIT<br>".$qP.";<br><br><br>";
    //if($cacheflush) $resP = $conn->CacheFlush($qP);
    $resP = $conn->Execute($qP);
    $allLinesP = $resP->fields[0];
    //echo " I ".$allLinesP." I ";
    $pages = floor($allLinesP/$rows);          
    if (($allLinesP%$rows) != 0) {             
    $pages++;                                  
    }
    if($sqltype == "PostgreSQL"){
	$toReturn=" LIMIT ".$rows." OFFSET ".$page*$rows;
    }else{
	$toReturn=" LIMIT ".$page*$rows.",".$rows;
    }
    return $toReturn;
}

function TTFprint(){
    global $TTF,$TTFa,$expor_excel,$linha,$GUI_LANG,$InAll;
	$expor_excel->MontaConteudo($linha+3, 0, $GUI_LANG['Total'].": ".$InAll[0]);

	$dodze=0;
	while (list($key,) = each ($TTF)) {
	    if(!empty($TTF[$key])){
	        $expor_excel->MontaConteudo($linha+3+$dodze, 1, $TTF[$key]);
		$dodze++;
	    }
	}
	$expor_excel->MontaConteudo($linha+3+$dodze, 0, $GUI_LANG['Altogether'].":");
	
	while (list($key,) = each ($TTFa)) {
	    if(!empty($TTFa[$key])){
		$expor_excel->MontaConteudo($linha+3+$dodze, 1, $TTFa[$key]);
		$dodze++;
	    }
	}
}

function Langname($filename){
    include $filename;
    return $GUI_LANG['LANG'].' '.$GUI_LANG['Charset'];
}

function ColorName($filenameC){
    include $filenameC;
    return $COLORS['SchemeName'];
}

// Описание номера из телефонной книги
// ------------------------------------------------------------------------------------------------------
//
function SetNumberDescription(){
    global $PhonebookDescription,$CallNumber,$GUI_LANG;
    global $toprint,$CallWay,$NumberIs;

    if(!empty($PhonebookDescription)){
	$NumberDescription = "<td><a href='../phonebook/?edit=".$CallNumber."' title='".$GUI_LANG['EditDescriptionOfTheNumber']." $NumberIs'>".$PhonebookDescription."</a></td>";
    }else{
	if(empty($toprint) && $CallNumber != 0 && $CallWay != 1){
	    $NumberDescription = "<td align=right><a href='../phonebook/?new=".$CallNumber."' title='".$GUI_LANG['AddDescriptionOfTheNumber']." $NumberIs'><sup>*</sup></a></td>";
	}
    }
    return $NumberDescription;
}

// Описание для внутреннего телефона
// ----------------------------------------------------------------------------
function setPhoneDescription(){
    global $intPhone,$NamedPhone,$noAbonents;

    if(!empty($NamedPhone) && !$noAbonents) $intPhoneDescription="($NamedPhone)";

    if(isset($intPhoneDescription)) return $intPhoneDescription;
	else return;
}


// Разные цвета для внутригородских, междугородних и международных звонков
// ------------------------------------------------------------------------------------------------------
//
function setCollColor(){
    global $toprint,$CallWay,$incoming,$FontColor,$FontColorEnd;
    global $LongDistanceCalls,$MobileCallsR,$InternationalCalls;
    global $COLORS,$CallNumber;

    $FontColor='';
    $FontColorEnd='';

    if(empty($toprint)){
    if($CallWay == "1" && $incoming!="3"){
	    $FontColor="<font ".$COLORS['IncomingCalls'].">";
	    $FontColorEnd="</font>";
	}elseif(ereg($MobileCallsR, $CallNumber)){
    	    $FontColor="<font ".$COLORS['MobileCalls'].">";
    	    $FontColorEnd="</font>";
	}elseif(ereg($InternationalCalls, $CallNumber)){
    	    $FontColor="<font ".$COLORS['InternationalCalls'].">";
	    $FontColorEnd="</font>";
	}elseif(ereg($LongDistanceCalls, $CallNumber)){
	    $FontColor="<font ".$COLORS['LongDistanceCalls'].">";
	    $FontColorEnd="</font>";
	}
    }
}

// Описание для внешней линии
function SetLineDescription() {
    global $NamedLine;
    
    if(!empty($NamedLine)) $coLineDescription="($NamedLine)";
    return $coLineDescription;
}

// Будем выводить комментарий к номеру в зависимости от
// того, имеет АТС АОН или нет.
function setNumberIs(){
    global $CallNumber,$CallWay,$IfAON;
    
    $NumberIs=$CallNumber;

    if($CallNumber == "0"){
	if($CallWay == "1"){
	    $NumberIs = $IfAON;
	}
    }
    return $NumberIs;
}
?>