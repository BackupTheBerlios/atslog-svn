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
if (!empty($rvar_debug))	$debug=translateHtml($rvar_debug);
if (!empty($rvar_lang))	$lang=translateHtml($rvar_lang);
if (!empty($rvar_color)) $color=translateHtml($rvar_color);
if (!empty($rvar_msg)) $msg=translateHtml($rvar_msg);
if (!empty($rvar_cacheflush)) $cacheflush=translateHtml($rvar_cacheflush);

	// Load language
	LanguageSetup($lang);
	
	Permissions();
	
	// Colors scheme   
	ColorSetup($color);

	connect_to_db();

	if (!checkpass()) {
	    nopass();
	}
	if (!hasprivilege("access", false)) {
    	    nopass();
	}

	hasprivilege("usersadmin", true);

	if (isset($_POST["add"]) && $_POST["add"])
	{
		if ($_POST["login"] == "")
		{
			header("Location: ?msg=3");
			die;
		}
		if ($_POST["passwd1"] != $_POST["passwd2"])
		{
			header("Location: ?msg=2");
			die;
		}
		if(!$demoMode){
		    $conn->Execute("insert into users (internally, login, password, lastname, firstname, secondname) values ('".$_POST["internally"]."', '".$_POST["login"]."', MD5('".$_POST["passwd1"]."'), '".$_POST["lastname"]."', '".$_POST["firstname"]."', '".$_POST["secondname"]."')");
		}
		setprivileges($_POST["login"]);
# ����� ���� �������� ������ �� ����� ����� �� ����.
		header("Location: ?cacheflush=1");
		die;
	}
	else if (isset($_POST["edit"]) && $_POST["edit"])
	{
		if(!$demoMode){
		    $conn->Execute("delete from usersgroups where login = '".$_POST["edit"]."'");
		}
		$q4="SELECT 0 FROM users WHERE login = '".$_POST["edit"]."'";
		$res = $conn->Execute($q4);
		if (isset($res->fields[0])){
		    if(!$demoMode){
			$conn->Execute("update users set internally='".$_POST["internally"]."', login = '".$_POST["login"]."', lastname = '".$_POST["lastname"]."', firstname = '".$_POST["firstname"]."', secondname = '".$_POST["secondname"]."' where login = '".$_POST["edit"]."'");
		    }
		}else{
		    if(!$demoMode){
			$conn->Execute("insert into users (internally, login, password, lastname, firstname, secondname) values ('".$_POST["internally"]."', '".$_POST["login"]."', MD5('".$_POST["passwd1"]."'), '".$_POST["lastname"]."', '".$_POST["firstname"]."', '".$_POST["secondname"]."')");
		    }
		}
		setprivileges($_POST["login"]);
# ����� ���� �������� ������ �� ����� ����� �� ����.
		header("Location: ?cacheflush=1");
		die;
	}
	else if (isset($_POST["resetpwd"]) && $_POST["resetpwd"])
	{
		if ($_POST["passwd1"] != $_POST["passwd2"])
		{
			header("Location: ?msg=2");
			die;
		}
		if(!$demoMode){
		    $conn->Execute("update users set password = MD5('".$_POST["passwd1"]."') where login = '".$_POST["resetpwd"]."'");
		}
# ����� ���� �������� ������ �� ����� ����� �� ����.
		header("Location: ?cacheflush=1");
		die;
	}
	else if (isset($_GET["delete"]) && $_GET["delete"])
	{
		if(!$demoMode){
	    	    $conn->Execute("delete from usersgroups where login = '".$_GET["delete"]."'");
		    $conn->Execute("delete from users where login = '".$_GET["delete"]."'");
		}
# ����� ���� �������� ������ �� ����� ����� �� ����.
		header("Location: ?cacheflush=1");
		die;
	}
$title=strtr($GUI_LANG['ManagementOfAbonents'],$GUI_LANG['UpperCase'],$GUI_LANG['LowerCase']);
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
    menucomplit("users");
?>
	<td width=50%>&nbsp;</td>
    </tr>
</table>
	</td>
    </tr>
    <tr>
	<TD>
<?php
	global $authrow;
    echo $GUI_LANG['Abonent'].": ".$authrow["login"]." (".$authrow["lastname"]." ".$authrow["firstname"]." ".$authrow["secondname"].")";
?>
	</td>
    </tr>
    <tr>
	<td>
<?php
	echo "<H1>".$GUI_LANG['Abonents'].":</H1>";
	if(isset($_GET["msg"])){
		switch ($_GET["msg"])
		{
			case 2:
				echo "<div><font color=red><b>".$GUI_LANG['PasswordsDoNotConsilient']."</b></font></div><br>";
				break;
				case 3:
				echo "<div><font color=red><b>".$GUI_LANG['EnterLoginName']."</b></font></div><br>";
				break;
		}
	}
	$q2="select internally, login, lastname, firstname, secondname from users order by login";
	if($debug) echo $q2."<br>";                   

	$q1="SELECT calls.internally,intphones.name
	FROM calls
	LEFT JOIN intphones ON intphones.intnumber = calls.internally
	group by calls.internally,intphones.name
	order by internally ASC";

	if($debug) echo $q1."<br>";

	$conn->setFetchMode(ADODB_FETCH_NUM);
	if(isset($cacheflush) && $cacheflush) $res1 = $conn->CacheFlush($q1);
	$res1 = $conn->CacheExecute($q1);
	if ($res1 && $res1->RecordCount() > 0) { 
	    while (!$res1->EOF) {
		$mamba=$res1->fields;
		$names=split(" ",$mamba[1]);
			$allIntern[]=array(0 => $mamba[0],1 => $mamba[0],5 => (isset($names[0])?$names[0]:''),6 => (isset($names[1])?$names[1]:''),7 => (isset($names[2])?$names[2]:''));
		$res1->MoveNext();
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
			if ($v[0]==$va[0]){
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
		    $allRows[]=$v;
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
	<table cellspacing=1 cellpadding=4 border=0><TR ".$COLORS['BaseTablesBgcolor']."><TH>&nbsp;</TH><TH>".$GUI_LANG['InternalPhone']."</TH><TH>".$GUI_LANG['Login']."</TH><TH>".$GUI_LANG['Initials']."</TH><TH>".$GUI_LANG['Permissions']."</TH><TH>&nbsp;</TH>";	
	if(!empty($allRows)){
	    while (list($key, $row) = each($allRows))
	    {
		if(isset($row[2]) or isset($row[3]) or isset($row[4])){
		    $comment=array($row[2],$row[3],$row[4]);
		    $shmayster=FALSE;
		}else{
		    $comment=array($row[5],$row[6],$row[7]);
		    $shmayster=TRUE;
		}

		echo "\n<TR ".$COLORS['BaseTrBgcolor']."><TD>";
		if (isset($_GET["edit"]) && $_GET["edit"] == $row[1])
		{
			echo "<IMG SRC=\"../include/img/rowselected.gif\" WidTH=19 HEIGHT=18 alt=\"\"></TD>\n";
			echo "<TD><FORM METHOD=POST STYLE=\"margin:0\"><INPUT TYPE=\"text\" NAME=\"internally\" VALUE=\"".htmlspecialchars($row[0])."\" SIZE=5></TD>\n";
			echo "<TD><INPUT TYPE=\"text\" NAME=\"login\" VALUE=\"".htmlspecialchars($row[1])."\" SIZE=10></TD>\n";
			echo "<TD><INPUT TYPE=\"text\" NAME=\"lastname\" VALUE=\"".htmlspecialchars($comment[0])."\"><br>\n";
			echo "<INPUT TYPE=\"text\" NAME=\"firstname\" VALUE=\"".htmlspecialchars($comment[1])."\"><br>\n";
			echo "<INPUT TYPE=\"text\" NAME=\"secondname\" VALUE=\"".htmlspecialchars($comment[2])."\"></TD>\n";
			showprivileges($row[1], false);
			echo "</TD><TD><INPUT TYPE=\"hidden\" NAME=\"edit\" VALUE=\"".$row[1]."\"><INPUT TYPE=IMAGE WidTH=16 HEIGHT=16 SRC=\"../include/img/save.gif\" ALT=\"".$GUI_LANG['Save']."\" onclick=\"submit();\" style=\"cursor:hand;\"><IMG WidTH=16 HEIGHT=16 src=\"../include/img/undo.gif\" alt=\"".$GUI_LANG['Cancel']."\" onclick=\"window.location = '?';\" style=\"cursor:hand;\"></FORM></TD>\n";
		}
		else
		{
			echo "<IMG alt=\"\" WidTH=19 HEIGHT=18 SRC=\"../include/img/row.gif\"></TD>";
			echo "<TD>".htmlspecialchars($row[0])."</TD>\n";

			echo "<TD>".htmlspecialchars($row[1])."<br>";
			if (isset($_GET["resetpwd"]) && $_GET["resetpwd"] == $row[1])
			{
				echo "<FORM METHOD=POST STYLE=\"margin:0\"><INPUT TYPE=\"hidden\" NAME=\"resetpwd\" VALUE=\"".$_GET["resetpwd"]."\">".$GUI_LANG['Password'].":<INPUT TYPE=\"password\" NAME=\"passwd1\"><BR>".$GUI_LANG['OnceAgain'].":<INPUT TYPE=\"password\" NAME=\"passwd2\">&nbsp;<INPUT TYPE=IMAGE WidTH=16 HEIGHT=16 SRC=\"../include/img/key.gif\" ALT=\"".$GUI_LANG['Save']."\" onclick=\"submit();\" style=\"cursor:hand;\">&nbsp;<IMG WidTH=16 HEIGHT=16 src=\"../include/img/undo.gif\" alt=\"".$GUI_LANG['Cancel']."\" onclick=\"window.location = '?';\" style=\"cursor:hand;\"></FORM>";
			}
			echo "</TD>\n<TD>\n";
			for ($i = 0; $i < 3; $i++)
			    if($shmayster){
				echo "<font ".$COLORS['HiddenFont'].">";
				echo htmlspecialchars($comment[$i])."<BR>\n";
				echo "</font>";
			    }else{
				echo htmlspecialchars($comment[$i])."<BR>\n";
			    }
			echo "</TD>\n";
			showprivileges($row[1], true);
			echo "</TD>\n<TD nowrap><IMG WidTH=16 HEIGHT=16 HSPACE=2 VSPACE=2 SRC=\"../include/img/button_edit.png\" ALT=\"".$GUI_LANG['Edit']."\" onclick=\"window.location = '?edit=".$row[1]."';\" style=\"cursor:hand;\">";
			if (!isset($_GET["resetpwd"]) || $_GET["resetpwd"] != $row[1])
				echo "<IMG WidTH=16 HEIGHT=16 SRC=\"../include/img/key.gif\" ALT=\"".$GUI_LANG['ChangePassword']."\" onclick=\"window.location = '?resetpwd=".$row[1]."';\" style=\"cursor:hand;\">";
			echo "<IMG WidTH=16 HEIGHT=16 HSPACE=2 VSPACE=2 SRC=\"../include/img/button_drop.png\" ALT=\"".$GUI_LANG['Delete']."\" onclick=\"if (window.confirm('".$GUI_LANG['RemoveTheAbonentWithLogin']." ".$row[1]."?')) window.location = '?delete=".$row[1]."';\" style=\"cursor:hand;\"></TD>";
		}
		echo "\n</TR>";
	    }
	}
	echo "<TR ".$COLORS['BaseTrBgcolor']."><TD><IMG WidTH=19 HEIGHT=18 SRC=\"../include/img/rownew.gif\" alt=\"\"><FORM METHOD=POST STYLE=\"margin:0\"></TD>";
	echo "<TD><INPUT TYPE=\"text\" NAME=\"internally\" SIZE=5></TD>";
	echo "<TD align=right>&nbsp;<br>".$GUI_LANG['Login'].":&nbsp;<INPUT TYPE=\"text\" NAME=\"login\" SIZE=10><BR>".$GUI_LANG['Password'].":&nbsp;<INPUT TYPE=\"password\" NAME=\"passwd1\" SIZE=10><BR>".$GUI_LANG['OnceAgain'].":&nbsp;<INPUT TYPE=\"password\" NAME=\"passwd2\" SIZE=10></TD>";
	echo "<TD align=right><div align=center>".$GUI_LANG['Initials'].":</div><INPUT TYPE=\"text\" NAME=\"lastname\" SIZE=10><br>";
	echo "<INPUT TYPE=\"text\" NAME=\"firstname\" SIZE=10><br>";
	echo "<INPUT TYPE=\"text\" NAME=\"secondname\" SIZE=10></TD>";
	showprivileges(null, false);
	echo "</TD>\n<TD><INPUT TYPE=\"hidden\" NAME=\"add\" VALUE=\"1\"><INPUT TYPE=IMAGE WidTH=16 HEIGHT=16 SRC=\"../include/img/new.gif\" ALT=\"".$GUI_LANG['Add']."\" onclick=\"submit();\" style=\"cursor:hand;\"></FORM></TD></TR>\n";
	echo "</TABLE>
		</td>
	    </tr>
	</table>
	";

    include("../include/set/footer.html");
?>
