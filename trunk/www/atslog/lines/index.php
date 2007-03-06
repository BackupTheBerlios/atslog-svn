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
if (!empty($rvar_debug)) $debug     = translateHtml($rvar_debug);
if (!empty($rvar_lang)) $lang     = translateHtml($rvar_lang);
if (!empty($rvar_color)) $color=translateHtml($rvar_color);
if (!empty($rvar_msg)) $msg=translateHtml($rvar_msg);
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

	hasprivilege("parameters", true);

	if (isset($_POST["add"]) && $_POST["add"])
	{
		if ($_POST["co"] == "")
		{
			header("Location: ?msg=3");
			die;
		}
		if ($_POST["name"] == "")
		{
			header("Location: ?msg=2");
			die;
		}
		if(!$demoMode){
		    $conn->Execute("insert into extlines (line, name) values ('".$_POST["co"]."', '".$_POST["name"]."')");
		}
# ����� ���� �������� ������ �� ����� ����� �� ����.
		header("Location: ?cacheflush=1");
		die;
	}
	else if (isset($_POST["edit"]) && $_POST["edit"])
	{
		if ($_POST["co"] == "")
		{
			header("Location: ?msg=3");
			die;
		}
		if ($_POST["name"] == "")
		{
			header("Location: ?msg=2");
			die;
		}
		$q4="SELECT 0 FROM extlines WHERE line = '".$_POST["edit"]."'";
		
		if (isset($res->fields[0])){
		    if(!$demoMode){
			$conn->Execute("update extlines set line='".$_POST["co"]."', name = '".$_POST["name"]."' where line = '".$_POST["edit"]."'");
		    }
		}else{
		    if(!$demoMode){
			$conn->Execute("insert into extlines (line, name) values ('".$_POST["co"]."', '".$_POST["name"]."')");
		    }
		}
# ����� ���� �������� ������ �� ����� ����� �� ����.
		header("Location: ?cacheflush=1");
		die;
	}
	else if (isset($_GET["delete"]) && $_GET["delete"])
	{
		$q5="delete from extlines where line = '".$_GET["delete"]."'";
		if(!$demoMode){
		    $conn->Execute($q5);
		}

# ����� ���� �������� ������ �� ����� ����� �� ����.
		header("Location: ?cacheflush=1");
		die;
	}
$title=strtr($GUI_LANG['ParametersOfLines'],$GUI_LANG['UpperCase'],$GUI_LANG['LowerCase']);
if(empty($export)) include("../include/set/header.html");
?>
<table cellpadding=0 cellspacing=0 border=0 width="100%">
    <tr><td colspan=2><?php
$user="";
if(IsDefaultPass($_SERVER['PHP_AUTH_USER'])) echo "<font color=red><b>".$GUI_LANG['ChangeDefaultAdminPassword']."</b></font><br>&nbsp;";
?></td>
    </tr>
    <tr>
<?php
    menucomplit("lines");
?>
	<td width=50%>&nbsp;</td>
    </tr>
</table>
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
	echo "<H1>".$GUI_LANG['Lines'].":</H1>";
	if(isset($_GET["msg"])){
		switch ($_GET["msg"])
		{
			case 2:
				echo "<div><font color=red><b>".$GUI_LANG['EnterTheDescriptionOfExternalLine']."</b></font></div><br>";
				break;
				case 3:
				echo "<div><font color=red><b>".$GUI_LANG['EnterALineNumber']."</b></font></div><br>";
				break;
		}
	}
	$q2="SELECT line, name from extlines order by line ASC";
	if($debug) echo $q2."<br>";

	$q1="SELECT co from calls group by co order by co ASC";
	if($debug) echo $q1."<br>";

	if(isset($cacheflush) && $cacheflush) $res1 = $conn->CacheFlush($q1);
	$res1 = $conn->CacheExecute($q1);
	if ($res1 && $res1->RecordCount() > 0) {
	    while ($arr1 = $res1->FetchRow()){
		$allIntern[]=$arr1[0];
	    }
	}
	
	$conn->setFetchMode(ADODB_FETCH_NUM);
	if(isset($cacheflush) && $cacheflush) $res = $conn->CacheFlush($q2);
	$res = $conn->CacheExecute($q2); 
	if ($res && $res->RecordCount() > 0) { 
	
	    while (!$res->EOF) {
		$allRows[]=$res->fields;
		$res->MoveNext();
	    }
	}

        if(!empty($allIntern)){
	    while (list($k, $v) = each($allIntern)) {
		if(!empty($allRows)){
		    reset($allRows);
		    while (list($ke, $va) = each($allRows)) {
			if ($v==$va[0]){
			    $dobavit=FALSE;
			    break;
			}else{
		    	    $dobavit=TRUE;
			}
		    }
		}else{
		    $dobavit=TRUE;
		}
		if($dobavit){
	    	    $tmpArr=array($v);
	    	    $allRows[]=$tmpArr;
		}
	    }
	}

	if(!empty($allRows)){
	    reset($allRows);
	    ksort($allRows);
	}
	echo "
	<table cellspacing=0 cellpadding=1 border=0>
	    <TR ".$COLORS['BaseBorderTableBgcolor'].">
		<td>
	<table cellspacing=1 cellpadding=4 border=0><TR ".$COLORS['BaseTablesBgcolor']."><TH>&nbsp;</TH><TH>".$GUI_LANG['ExternalLines']."</TH><TH>".$GUI_LANG['Description']."</TH><TH>&nbsp;</TH>";
	if(!empty($allRows)){
	    while (list($key, $row) = each($allRows))
	    {

		echo "\n<TR ".$COLORS['BaseTrBgcolor']."><TD>";
		if (isset($_GET["edit"]) && $_GET["edit"] == $row[0])
		{
			echo "<IMG alt=\"\" SRC=\"../include/img/rowselected.gif\" WidTH=19 HEIGHT=18></TD>\n";
			echo "<TD><FORM METHOD=POST STYLE=\"margin:0\"><INPUT TYPE=\"text\" NAME=\"co\" VALUE=\"".htmlspecialchars($row[0])."\" SIZE=5></TD>\n";
			echo "<TD><INPUT TYPE=\"text\" NAME=\"name\" VALUE=\"".(isset($row[1])?htmlspecialchars($row[1]):'')."\" SIZE=30></TD>\n";
			echo "<TD><INPUT TYPE=\"hidden\" NAME=\"edit\" VALUE=\"".$row[0]."\"><INPUT TYPE=IMAGE WidTH=16 HEIGHT=16 SRC=\"../include/img/save.gif\" ALT=\"".$GUI_LANG['Save']."\" onclick=\"submit();\" style=\"cursor:hand;\"><IMG WidTH=16 HEIGHT=16 src=\"../include/img/undo.gif\" alt=\"".$GUI_LANG['Cancel']."\" onclick=\"window.location = '?';\" style=\"cursor:hand;\"></FORM></TD>\n";
		}
		else
		{
			echo "<IMG WidTH=19 HEIGHT=18 alt=\"\" SRC=\"../include/img/row.gif\"></TD>";
			echo "<TD>".htmlspecialchars($row[0])."</TD>\n";

			echo "<TD>".(isset($row[1])?htmlspecialchars($row[1]):'')."</TD>";
			echo "<TD><IMG WidTH=16 HEIGHT=16 HSPACE=2 VSPACE=2 SRC=\"../include/img/button_edit.png\" ALT=\"".$GUI_LANG['Edit']."\" onclick=\"window.location = '?edit=".$row[0]."';\" style=\"cursor:hand;\">";
			echo "<IMG WidTH=16 HEIGHT=16 HSPACE=2 VSPACE=2 SRC=\"../include/img/button_drop.png\" ALT=\"".$GUI_LANG['Delete']."\" onclick=\"if (window.confirm('".$GUI_LANG['DeleteDescriptionOfExternalLine']." ".$row[0]."?')) window.location = '?delete=".$row[0]."';\" style=\"cursor:hand;\"></TD>";
		}
		echo "\n</TR>";
	    }
	}
	echo "<TR ".$COLORS['BaseTrBgcolor']."><TD><IMG WidTH=19 HEIGHT=18 SRC=\"../include/img/rownew.gif\" alt=\"\"><FORM METHOD=POST STYLE=\"margin:0\"></TD>";
	echo "<TD><INPUT TYPE=\"text\" NAME=\"co\" SIZE=5></TD>";
	echo "<TD><INPUT TYPE=\"text\" NAME=\"name\" SIZE=30></TD>";
	echo "<TD><INPUT TYPE=\"hidden\" NAME=\"add\" VALUE=\"1\"><INPUT TYPE=IMAGE WidTH=16 HEIGHT=16 SRC=\"../include/img/new.gif\" ALT=\"".$GUI_LANG['Add']."\" onclick=\"submit();\" style=\"cursor:hand;\"></FORM></TD></TR>\n";
	echo "</TABLE>
		</td>
	    </tr>
	</table>
	";

    include("../include/set/footer.html");
?>
