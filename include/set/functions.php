<?

 /*

  Опишем общие параметры
  
 */

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
    global $COLORS;

    if(!empty($color)){
	include("../include/colors/".$color.".php");
    }else{
	include("../include/colors/classic.php");
    }
}

// Найдём пароль в дебрях переменных от HTTP сервера
// ----------------------------------------------------------------------------
if (empty($PHP_AUTH_PW)) {
    if (!empty($_SERVER) && isset($_SERVER['PHP_AUTH_PW'])) {
	$PHP_AUTH_PW = $_SERVER['PHP_AUTH_PW'];
    }elseif(isset($REMOTE_PASSWORD)) {
	$PHP_AUTH_PW = $REMOTE_PASSWORD;
    }elseif(!empty($_ENV) && isset($_ENV['REMOTE_PASSWORD'])) {
	$PHP_AUTH_PW = $_ENV['REMOTE_PASSWORD'];
    }elseif (@getenv('REMOTE_PASSWORD')) {
	$PHP_AUTH_PW = getenv('REMOTE_PASSWORD');
    }elseif(isset($AUTH_PASSWORD)) {
	$PHP_AUTH_PW = $AUTH_PASSWORD;
    }elseif(!empty($_ENV) && isset($_ENV['AUTH_PASSWORD'])) {
	$PHP_AUTH_PW = $_ENV['AUTH_PASSWORD'];
    }elseif(@getenv('AUTH_PASSWORD')) {
	$PHP_AUTH_PW = getenv('AUTH_PASSWORD');
    }
}
// ----------------------------------------------------------------------------

 /*

   Соединяемся c SQL посредством ADODB

 */
import_request_variables("gPc","rvar_");
if(!empty($rvar_debug)) $debug=translateHtml($rvar_debug);
include('../include/adodb/adodb.inc.php'); // load code common to ADOdb
// Создадим каталог для кеширования запросов.
if(!file_exists($ADODB_CACHE_DIR)) mkdir($ADODB_CACHE_DIR, 0777); 
$conn = &ADONewConnection($sqltype);   // create a connection 
$conn->cacheSecs = $ADODB_CACHE_TTL;   // время жизни кеша SQL запроса
$conn->PConnect($sqlhost,$sqlmasteruser,$sqlmaspasswd,$sqldatabase); // connect to SQL, agora db
if($debug==2) $conn->debug = true;


 /*
   Опишем функции
 */


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
	    echo ("<option value=\"$m_num\"$Month[$m_num]>".$GUI_LANG['Month'.$m_num]."</option>\n");
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

	$q2="select Internally, Login, Lastname, Firstname, Secondname from users where Login ='".$_SERVER['PHP_AUTH_USER']."'";
	$res=$conn->getRow($q2);
	$authrow=$res;
}

//
// Проверим привелегии пользователя и выдадим ему ссылки
// ----------------------------------------------------------------------------
function menucomplit($from){

    global $local_cacheflush,$local_order,$local_sortBy,$order,$sortBy,$GUI_LANG,
    $local_page,$page,$local_search,$search,$local_export;

    echo "<td width=50% valign=top>";

    echo "<a href=\"".complitLink($local_cacheflush="1",$local_order=$order,$local_sortBy=$sortBy,$local_page=$page,$local_search=$search)."\" title=\"".$GUI_LANG['ToClearTheCacheAndRefreshThePage']."\">".$GUI_LANG['Refresh']."</a><br>";

    if ($from != "settings"){
	echo("<a href=\"../settings/\" title=\"".$GUI_LANG['UsersSettings']."\">".$GUI_LANG['Settings']."</a><br>");
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
    if ($from == "calls"){
	echo("<a href=\"".complitLink($local_export="excel",$local_order=$order,$local_sortBy=$sortBy,$local_page=$page,$local_search=$search)."\" title=\"".$GUI_LANG['ExportDataInExcelFormat']."\">".$GUI_LANG['ExportInExcel']."</a><br>");
    }
    echo "</td>";
}

function hasprivilege($priv, $redirect = false, $userName = false)
{
	if(!$userName){
	    $userName = $_SERVER['PHP_AUTH_USER'];
	}

	global $authrow,$conn;
	$q1="select 0 from usersgroups where Login = '".$userName."' and Groups = '$priv'";
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

	$q3="select 0 from users where Login = '".$_SERVER['PHP_AUTH_USER']."' and Password = PASSWORD('".$PHP_AUTH_PW."')";
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

	    $qC="INSERT INTO unauth (username, pass, ip,x_forwardeded_for) VALUES ('".$_SERVER['PHP_AUTH_USER']."', '".$PHP_AUTH_PW."','".$_SERVER['REMOTE_ADDR']."', '".$_SERVER['HTTP_X_FORWARDED_FOR']."')";
	    $conn->Execute($qC);
	    //sleep(2);
	}
	return $toReturn;
}

function IsDefaultPass($user)
{
	global $conn,$demoMode;

	$toReturn=FALSE;
	if($user=="atslog" && !$demoMode){
	    $q1="select 0 from users where Login = '".$user."' and Password = PASSWORD('atslog')";
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

function showprivileges($Login, $readonly)
{
    global $privileges;
    for ($i = 0; $i < count($privileges); $i+=2)
    {
	if(hasprivilege($privileges[$i+1],false, $Login) && isset($Login)){
	    echo ($i ? "<BR>" : "<TD>")."<INPUT TYPE=\"checkbox\" NAME=\"priv".($i/2)."\"".($readonly ? " disabled" : "")." checked".">".$privileges[$i];
	}else{
	    echo ($i ? "<BR>" : "<TD>")."<INPUT TYPE=\"checkbox\" NAME=\"priv".($i/2)."\"".($readonly ? " disabled" : "")."".">".$privileges[$i];
	}
    }
}

function setprivileges($Login)
{
    global $privileges,$conn,$demoMode;
    for ($i = 0; $i < count($privileges); $i+=2){
	if ($_POST["priv".($i/2)]){
	    if ($debug) echo "priv".($i/2)." = ".$_POST["priv".($i/2)]."<BR>";
	    if(!$demoMode){
		$q4="insert into usersgroups (Login, Groups) values ('$Login', '".$privileges[$i+1]."')";
		$conn->Execute($q4);
	    }
	}
    }
}

function pechat_ishodnyh()
{
    global $from_date,$to_date,$int_echo,$incoming,
    $additionalEcho,$NamedLine,$co,$int,$num,$toprint,
    $CityOnly,$noMobLine,$noNationalLine,$telephone,
    $vhodjashij,$GUI_LANG;

    echo("<h3>".strtr($GUI_LANG['TypeOfReport'],$GUI_LANG['UpperCase'],$GUI_LANG['LowerCase']).": $int_echo<br>");
    if(!empty($incoming)) echo ("$additionalEcho<br>");
    if(empty($NamedLine)) $NamedLine=$co;
	else $NamedLine="$co ($NamedLine)";
    if(!empty($co)) echo ($GUI_LANG['ThroughAnExternalLine'].": $NamedLine<br>");
    if(!empty($int)){
	if(empty($telephone)) {
	    $telephone=$int;
	}else{
	    $telephone="$int ($telephone)";
	}

	echo ($GUI_LANG['FromInternalPhone'].": $telephone<br>");
    }
    if($num!=''){
	if($num=='0'){
		$numEcho=$vhodjashij;
	    }else{
		$numEcho=$num;
	    }
	echo (strtr($GUI_LANG['Number'],$GUI_LANG['UpperCase'],$GUI_LANG['LowerCase']).": $numEcho<br>");
    }
    if ($toprint=="yes") echo (strtr($GUI_LANG['PeriodFrom'],$GUI_LANG['UpperCase'],$GUI_LANG['LowerCase']).": $from_date ".$GUI_LANG['To'].": $to_date<br>");
    if ($toprint=="yes" && $CityOnly==1) echo $GUI_LANG['CityCallsOnly']."<br>";
    if ($toprint=="yes" && $CityOnly==2) echo $GUI_LANG['AreExcludedCity']."<br>";
    if ($toprint=="yes" && $noMobLine) echo $GUI_LANG['AreExcludedCellularCommunication']."<br>";
    if ($toprint=="yes" && $noNationalLine) echo $GUI_LANG['AreExcludedLongDistance']."<br>";
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
			    $now_img="&nbsp;&nbsp;<img src=\"../include/img/arrowAsc.gif\" width=7 height=7 border=0>";
			}else{
			    $now_order="DESC";
			    $now_title=$GUI_LANG['SortByDecrease'];
			    $now_img="&nbsp;&nbsp;<img src=\"../include/img/arrowDesc.gif\" width=7 height=7 border=0>";
			}
		}else{
		    if ($baseOrder=="DESC"){
			$now_order="DESC";
			$now_title=$GUI_LANG['SortByDecrease'];
		    }else{
			$now_order="ASC";
			$now_title=$GUI_LANG['SortByIncrease'];
		    }
		}
		echo (" <a href=\"".complitLink($local_order=$now_order,$local_sortBy="$fname",$local_search=$search)."\" title=\"".$now_title."\">$thname</a>".$now_img);
	}else{
		echo ($thname);
	}
}

function totalTableFooter($field,$returnType){

	global $conn,$from_date,$to_date,$additionalReq,$LocalCalls,
	$isNoCityCalls,$LongDistanceCalls,$MobileCallsR,
	$InternationalCalls,$debug,$cacheflush,$GUI_LANG;

	switch($field){
	    case "4":
		// Всего: общее количество звонков
		$q="SELECT COUNT(*) from calls where ((calls.TimeOfCall>='".$from_date."') AND (calls.TimeOfCall<='".$to_date."')".$additionalReq.")";
		break;
	    case "5":
		// Всего: городские
		$q="SELECT SUM(`calls`.`Duration`),COUNT(*) from calls where ((calls.TimeOfCall>='".$from_date."') AND (calls.TimeOfCall<='".$to_date."')".$additionalReq." AND (calls.Number REGEXP '".$LocalCalls."'))";
		$Qcomment=$GUI_LANG['City'];
		break;
	    case "6":
		// Всего: межгород
		$q="SELECT SUM(`calls`.`Duration`),COUNT(*) from calls where ((calls.TimeOfCall>='".$from_date."') AND (calls.TimeOfCall<='".$to_date."')".$additionalReq." AND (calls.Number REGEXP '".$LongDistanceCalls."' AND calls.Number NOT REGEXP '".$MobileCallsR."'))";
		$Qcomment=$GUI_LANG['Trunk'];
		break;
	    case "7":
		// Всего:  мобильная связь
		$q="SELECT SUM(`calls`.`Duration`),COUNT(*) from calls where ((TimeOfCall>='".$from_date."') AND (TimeOfCall<='".$to_date."')".$additionalReq." AND (calls.Number REGEXP '".$MobileCallsR."'))";
		$Qcomment=$GUI_LANG['Mobile'];
		break;
	    case "8":
		// Всего: международная связь
		$q="SELECT SUM(`calls`.`Duration`),COUNT(*) from calls where ((TimeOfCall>='".$from_date."') AND (TimeOfCall<='".$to_date."')".$additionalReq." AND (calls.Number REGEXP '".$InternationalCalls."'))";
		$Qcomment=$GUI_LANG['LongDistance'];
		break;
	}
	if($cacheflush) $res = $conn->CacheFlush($q);
	$local_cacheflush="";
	$res = $conn->CacheExecute($q);

	if ($debug)  echo "<hr>".$q."<br><hr>";

	if($returnType==1){
	    $results=$res->fields[0];
	}else{
	    $total=$res->fields;

	    if($total[0]){                                              
		$results="$Qcomment: ";
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
		    $results.="</b><br>\n";
		}
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
    $CityOnly,$noMobLine,$noNationalLine,$debug,$debugMode,
    $num,$rows;

    global $local_day,$local_mon,$local_year,$local_day2,
    $local_mon2,$local_year2,$local_order,
    $local_sortBy,$local_type,$local_co,$local_int,$local_toprint,
    $local_incoming,$local_CityOnly,$local_noMobLine,
    $local_noNationalLine,$local_debug,$local_cacheflush,
    $local_num,$local_page,$local_search,$local_export;

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
    $cL['CityOnly']= (empty($local_CityOnly)) ? $CityOnly : $local_CityOnly;
    $cL['noMobLine']= (empty($local_noMobLine))	? $noMobLine : $local_noMobLine;
    $cL['noNationalLine'] = (empty($local_noNationalLine)) ? $noNationalLine : $local_noNationalLine;
    $cL['num'] = ($local_num == "") ? "$num" : "$local_num";
    $cL['cacheflush'] = (empty($local_cacheflush)) ? $cacheflush : $local_cacheflush;
    if(!empty($local_page)) $cL['page'] = $local_page;
    if(!empty($rows)) $cL['rows'] = $rows;
    if(!empty($local_search)) $cL['search'] = $local_search;
    if(!empty($local_export)) $cL['export'] = $local_export;

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
    $local_CityOnly="";
    $local_noMobLine="";
    $local_noNationalLine="";
    $local_debug="";
    $local_cacheflush="";
    $local_num="";
    $local_page="";
    $local_export="";

    return $complitLine;


}


function pagesNavigator($pages,$page){
    global $local_page,$local_search,$GUI_LANG,$toprint,$local_order,$order,$local_sortBy,$sortBy,$search;
?>
<table cellpadding=1 cellspacing=0 border=0 align=center>
	<tr>
<?php
// Печатаем навигатор по страницам

if ($pages>1 && !$toprint){
	if ($page>0){
		print("<td><a href=\"".complitLink($local_page="0",$local_order=$order,$local_sortBy=$sortBy,$local_search=$search)."\" title=\"".$GUI_LANG['FirstPage']." (1)\"><font face=\"Webdings\">&#57;</font></a></td>");
	}

	if ($page>1){
		$prevGoPage = $page-1;
		print("<td><a href=\"".complitLink($local_page="$prevGoPage",$local_order=$order,$local_sortBy=$sortBy,$local_search=$search)."\" title=\"".$GUI_LANG['PreviousPage']." ($page)\"><font face=\"Webdings\">&#51;</font></a></td>");
	}

	print("
		<td>".$GUI_LANG['Pages'].":</td>
	");

	if ($page<($pages-2)){
		$nextGoPage = $page+1;
		print("<td><a href=\"".complitLink($local_page="$nextGoPage",$local_order=$order,$local_sortBy=$sortBy,$local_search=$search)."\" title=\"".$GUI_LANG['NextPage']." (".($nextGoPage+1).")\"><font face=\"Webdings\">&#52;</font></a></td>");
	}

	if ($page<($pages-1)){
		$nextGoPage = $pages-1;
		print("<td><a href=\"".complitLink($local_page="$nextGoPage",$local_order=$order,$local_sortBy=$sortBy,$local_search=$search)."\" title=\"".$GUI_LANG['LastPage']." ($pages)\"><font face=\"Webdings\">&#58;</font></a></td>");
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
	    print("<a href=\"".complitLink($local_page="$prevList",$local_order=$order,$local_sortBy=$sortBy,$local_search=$search)."\" title=\"".$GUI_LANG['PreviousListOfPages']."\"><font face=\"Webdings\">&#55;</font>...</a> ");
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
		print("<a href=\"".complitLink($local_page="$nextList",$local_order=$order,$local_sortBy=$sortBy,$local_search=$search)."\" title=\"".$GUI_LANG['NextListOfPages']."\">...<font face=\"Webdings\">&#56;</font></a>");
	}
    }
}

//echo("</td></tr><tr><td>pages = $pages, page = $page, startLine = $startLine, lastLine = $lastLine, startList = $startList, stopList = $stopList");

?>
</td></tr>
</table>

<?
}

// Функция возвращает строку с LIMIT.
// Нужна для разбиения на страницы.
// ----------------------------------------------------------------------------
function getLimits($qP){
    global $conn,$debug,$cacheflush,$pages,$page,$rows;

    if($debug) echo "// Information for LIMIT<br>".$qP.";<br><br><br>";
    //if($cacheflush) $resP = $conn->CacheFlush($qP);
    $resP = $conn->Execute($qP);
    $allLinesP = $resP->fields[0];
    //echo " I ".$allLinesP." I ";
    $pages = floor($allLinesP/$rows);          
    if (($allLinesP%$rows) != 0) {             
    $pages++;                                  
    }                                          
    return " LIMIT ".$page*$rows.",".$rows;
}

function TTFprint(){
    global $TTF,$TTFa,$expor_excel,$linha,$GUI_LANG,$InAll;
	$expor_excel->MontaConteudo($linha+3, 0, $GUI_LANG['Altogether'].": ".$InAll[0]);

	$dodze=0;
	while (list($key,) = each ($TTF)) {
	    if(!empty($TTF[$key])){
	        $expor_excel->MontaConteudo($linha+3+$dodze, 1, $TTF[$key]);
		$dodze++;
	    }
	}
	$expor_excel->MontaConteudo($linha+3+$dodze, 0, $GUI_LANG['Total'].":");
	
	while (list($key,) = each ($TTFa)) {
	    if(!empty($TTFa[$key])){
		$expor_excel->MontaConteudo($linha+3+$dodze, 1, $TTFa[$key]);
		$dodze++;
	    }
	}
}

function LangName($filename){
    include $filename;
    return $GUI_LANG['LANG'];
}

function ColorName($filenameC){
    include $filenameC;
    return $COLORS['SchemeName'];
}

?>