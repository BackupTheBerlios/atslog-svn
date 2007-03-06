<?php

 /*                              
                                  
    ���������                     
                                     
 */                              
                                      
include("../include/config.inc.php");
 /* 

    �������

 */
 
include('../include/set/functions.php');

// ���������� ������������ ������� ���������� �� ���� ������������ � ������������� �
// ������� � ������ register_globals
//
import_request_variables("gPc", "rvar_");
if (!empty($rvar_debug))		$debug     = translateHtml($rvar_debug);
if (!empty($rvar_lang))                 $lang     = translateHtml($rvar_lang);
if (!empty($rvar_color)) $color=translateHtml($rvar_color);
if (!empty($rvar_onlyme)) $onlyme=translateHtml($rvar_onlyme);
if (!empty($rvar_msg)) $msg=translateHtml($rvar_msg);
if (!empty($rvar_new)) $new=translateHtml($rvar_new);
if (!empty($rvar_export)) $export=translateHtml($rvar_export);
if (!empty($rvar_cacheflush)) $cacheflush=translateHtml($rvar_cacheflush);


	// Load language
	LanguageSetup($lang);
	
	// Colors scheme   
	ColorSetup($color);

	connect_to_db();

	if (!checkpass()) {
	    nopass();
	}
	if (!hasprivilege("access", false)) {
    	    nopass();
	}

	if (isset($_POST["add"]) && $_POST["add"])
	{
		if ($_POST["number"] == "")
		{
			header("Location: ?msg=3");
			die;
		}
		if ($_POST["description"] == "")
		{
			header("Location: ?msg=2");
			die;
		}
		if(!$demoMode){
		    if(isset($onlyme) && $onlyme){
			$onliLogin="'".$_SERVER['PHP_AUTH_USER']."'";
		    }else{
			$onliLogin="NULL";
		    }
		    $q6="insert into phonebook (login, number, description) values (".$onliLogin.",'".$_POST["number"]."', '".$_POST["description"]."')";
		    if($debug) echo $q6."<br>";
		    $conn->Execute($q6);
		}
# ����� ���� �������� ������ �� ����� ����� �� ����.
		header("Location: ?cacheflush=1");
		die;
	}
	else if (isset($_POST["edit"]) && $_POST["edit"])
	{
		if ($_POST["number"] == "")
		{
	    	    header("Location: ?msg=3");
		    die;
		}
		if ($_POST["description"] == "")
		{
		    header("Location: ?msg=2");
		    die;
		}
		$q4="SELECT 0 FROM phonebook WHERE number = '".$_POST["edit"]."'";
		if($debug) echo $q4."<br>";
				
		$res = $conn->Execute($q4);

		if(isset($onlyme) && $onlyme){
		    $onliLogin="'".$_SERVER['PHP_AUTH_USER']."'";
		}else{
		    $onliLogin="NULL";
		}

		if (isset($res->fields[0])){
		    if(!$demoMode){
			$conn->Execute("update phonebook set login = $onliLogin, number='".$_POST["number"]."', description = '".$_POST["description"]."' where number = '".$_POST["edit"]."'");
		    }
		}else{
		    if(!$demoMode){
			$q7="insert into phonebook (login, number, description) values (".$onliLogin.",'".$_POST["number"]."', '".$_POST["description"]."')";
			if($debug) echo $q7."<br>";
			$conn->Execute($q7);
		    }
		}
# ����� ���� �������� ������ �� ����� ����� �� ����.
		header("Location: ?cacheflush=1");
		die;
	}
	else if (isset($_GET["delete"]) && $_GET["delete"])
	{
		$q5="delete from phonebook where number = '".$_GET["delete"]."'";
		if($debug) echo $q5."<br>";
		if(!$demoMode){
		    $conn->Execute($q5);
		}
		
# ����� ���� �������� ������ �� ����� ����� �� ����.
		header("Location: ?cacheflush=1");
		die;
	}
$title=strtr($GUI_LANG['PhoneBook'],$GUI_LANG['UpperCase'],$GUI_LANG['LowerCase']);
if(empty($export)) include("../include/set/header.html");

// Export                                   
if(isset($export) && $export=="excel") {
    include("../include/export/2excel.php");
    $expor_excel = new MID_SQLPARAExel;
}

if(empty($export)) {
?>
<div>
<table cellpadding=0 cellspacing=0 border=0 width="100%">
    <tr><td colspan=2><?php
$user="";
if(IsDefaultPass($_SERVER['PHP_AUTH_USER'])) echo "<font color=red><b>".$GUI_LANG['ChangeDefaultAdminPassword']."</b></font><br>&nbsp;";
?></td>
    </tr>
    <tr>
<?php
    menucomplit("phonebook");
?>
	<td width=50%>&nbsp;</td>
    </tr>
</table>
	    </div>
	</td>
    </tr>
    <tr>
	<TD>
<?php
    echo $GUI_LANG['Abonent'].": ".$authrow["login"]." (".$authrow["lastname"]." ".$authrow["firstname"]." ".$authrow["secondname"].")";
?>
	</td>
    </tr>
    <tr>
	<td>
<?php
	echo "<H1>".$GUI_LANG['PhoneBook'].":</H1>";
}
	if(isset($_GET["msg"])){
		switch ($_GET["msg"])
		{
			case 2:
			echo "<div><font color=red><b>".$GUI_LANG['EnterDescriptionOfTheNumber']."</b></font></div><br>";
			break;
			case 3:
			echo "<div><font color=red><b>".$GUI_LANG['EnterAPhoneNumber']."</b></font></div><br>";
			break;
		}
	}
	$q2="SELECT
	login,number,description
	from phonebook
	where phonebook.login = '".$_SERVER['PHP_AUTH_USER']."'
	   OR phonebook.login IS NULL
	order by number ASC";
	if($debug) echo $q2."<br>";

	$conn->setFetchMode(ADODB_FETCH_NUM);
	if(isset($cacheflush) && $cacheflush) $res = $conn->CacheFlush($q2);
	$res = $conn->CacheExecute($q2);              
	if ($res && $res->RecordCount() > 0) { 
	    while (!$res->EOF) {
		$allRows[]=$res->fields;
		$res->MoveNext();
	    }
	}

	if(!empty($allRows)){
	    reset($allRows);
	    ksort($allRows);
	}

if(empty($export)) {
	echo "
	<table cellspacing=0 cellpadding=1 border=0>
	    <TR ".$COLORS['BaseBorderTableBgcolor'].">
		<td>
	<table cellspacing=1 cellpadding=4 border=0><TR ".$COLORS['BaseTablesBgcolor']."><TH>&nbsp;</TH><TH>".$GUI_LANG['TheDescriptionIsAccessible'].":</TH><TH>".$GUI_LANG['PhoneNumber']."</TH><TH>".$GUI_LANG['Description']."</TH><TH>&nbsp;</TH>";
}else{
	$expor_excel->MontaConteudo(0, 0,$GUI_LANG['PhoneNumber']); 
	$expor_excel->MontaConteudo(0, 1,$GUI_LANG['Description']);
	$expor_excel->MontaConteudo(1, 0, "   ");
	$expor_excel->MontaConteudo(1, 1, "   ");
	$expor_excel->mid_sqlparaexcel();
}

	if(!empty($allRows)){
	    $linha=0;
	    while (list($key, $row) = each($allRows))
	    {
		$FontColor='';
		$FontColorEnd='';

		if(ereg($MobileCallsR, $row[1])){
		    $FontColor="<font ".$COLORS['MobileCalls'].">";
    		    $FontColorEng="</font>";
    		}elseif(ereg($InternationalCalls, $row[1])){
		    $FontColor="<font ".$COLORS['InternationalCalls'].">";
    		    $FontColorEng="</font>";
    		}elseif(ereg($LongDistanceCalls, $row[1])){
	    	    $FontColor="<font ".$COLORS['LongDistanceCalls'].">";
		    $FontColorEng="</font>";
		}

		if(empty($export)) echo "\n<TR ".$COLORS['BaseTrBgcolor'].">";
		if (isset($_GET["edit"]) && $_GET["edit"] == $row[1])
		{
		    if(empty($export)) {
			if($row[0]) $checkMe[0]=" CHECKED"; else $checkMe[1]=" CHECKED";
			echo "<TD><IMG alt=\"\" SRC=\"../include/img/rowselected.gif\" WidTH=19 HEIGHT=18></TD>\n";
			echo "<TD><FORM METHOD=POST STYLE=\"margin:0\"><INPUT TYPE=RADIO NAME=\"onlyme\" VALUE=1".(isset($checkMe[0])?$checkMe[0]:'').">".$GUI_LANG['OnlyForMe']." <INPUT TYPE=RADIO NAME=\"onlyme\" VALUE=0".(isset($checkMe[1])?$checkMe[1]:'').">".$GUI_LANG['ForEverybody']."</TD>";
			echo "<TD><INPUT TYPE=\"text\" NAME=\"number\" VALUE=\"".htmlspecialchars($row[1])."\" SIZE=30 maxlength=100></TD>\n";
			echo "<TD><INPUT TYPE=\"text\" NAME=\"description\" VALUE=\"".htmlspecialchars($row[2])."\" SIZE=30 maxlength=255></TD>\n";
			echo "<TD><INPUT TYPE=\"hidden\" NAME=\"edit\" VALUE=\"".$row[1]."\"><INPUT TYPE=IMAGE WidTH=16 HEIGHT=16 SRC=\"../include/img/save.gif\" ALT=\"".$GUI_LANG['Save']."\" onclick=\"submit();\" style=\"cursor:hand;\"><IMG WidTH=16 HEIGHT=16 src=\"../include/img/undo.gif\" alt=\"".$GUI_LANG['Cancel']."\" onclick=\"window.location = '?';\" style=\"cursor:hand;\"></FORM></TD>\n";
		    }
		}
		else
		{
		    if(empty($export)) {
			echo "<TD><IMG WidTH=19 HEIGHT=18 SRC=\"../include/img/row.gif\" alt=\"\"></TD>";
			if($row[0]) $viewMe=$GUI_LANG['OnlyForMe']; else $viewMe=$GUI_LANG['ForEverybody'];
			echo "<TD>$viewMe</TD>";
			echo "<TD>$FontColor".htmlspecialchars($row[1])."$FontColorEnd</TD>\n";

			echo "<TD>".htmlspecialchars($row[2])."</TD>";
			echo "<TD><IMG WidTH=16 HEIGHT=16 HSPACE=2 VSPACE=2 SRC=\"../include/img/button_edit.png\" ALT=\"".$GUI_LANG['Edit']."\" onclick=\"window.location = '?edit=".$row[1]."';\" style=\"cursor:hand;\">";
			echo "<IMG WidTH=16 HEIGHT=16 HSPACE=2 VSPACE=2 SRC=\"../include/img/button_drop.png\" ALT=\"".$GUI_LANG['Delete']."\" onclick=\"if (window.confirm('".$GUI_LANG['DeleteDescriptionOfThePhoneNumber']." ".$row[1]."?')) window.location = '?delete=".$row[1]."';\" style=\"cursor:hand;\"></TD>";
		    }else{
			$expor_excel->MontaConteudo($linha+2, 0, $row[1]);
			$expor_excel->MontaConteudo($linha+2, 1, $row[2]);
		    }

		}

		if(empty($export)) echo "\n</TR>";
		$linha++;
	    }
	}
	if(empty($export)) {
	    echo "<TR ".$COLORS['BaseTrBgcolor'].">";
	    echo "<TD><IMG WidTH=19 HEIGHT=18 SRC=\"../include/img/rownew.gif\" alt=\"\"><FORM METHOD=POST STYLE=\"margin:0\"></TD>";
	    echo "<TD><INPUT TYPE=RADIO NAME=\"onlyme\" VALUE=1 CHECKED>".$GUI_LANG['OnlyForMe']." <INPUT TYPE=RADIO NAME=\"onlyme\" VALUE=0>".$GUI_LANG['ForEverybody']."</TD>"; 
	    echo "<TD><INPUT TYPE=\"text\" NAME=\"number\" SIZE=30 maxlength=100 VALUE=\"".(isset($new)?$new:'')."\"></TD>";
	    echo "<TD><INPUT TYPE=\"text\" NAME=\"description\" SIZE=30 maxlength=255></TD>";
	    echo "<TD><INPUT TYPE=\"hidden\" NAME=\"add\" VALUE=\"1\"><INPUT TYPE=IMAGE WidTH=16 HEIGHT=16 SRC=\"../include/img/new.gif\" ALT=\"".$GUI_LANG['Add']."\" onclick=\"submit();\" style=\"cursor:hand;\"></FORM></TD></TR>\n";
	    echo "</TABLE>
			</td>
		    </tr>
		</table>
	    ";
	}

    if(empty($export)){
	include("../include/set/footer.html");
    }else{
	$expor_excel->GeraArquivo();
    }

?>
