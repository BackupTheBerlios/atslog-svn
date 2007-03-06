<?php

if(isset($_POST['newLang'])){
    setcookie("lang",$_POST['newLang'],time() + 60*60*24*365,"/");
    $setupLang = $_POST['newLang'];
}

if(isset($_POST['newColor'])){
    setcookie("color",$_POST['newColor'],time() + 60*60*24*365,"/");
    $setupColor = $_POST['newColor'];
}

if(isset($_POST['baseOrder'])){
    setcookie("baseOrder",$_POST['baseOrder'],time() + 60*60*24*365,"/");
}

if(isset($_POST['rows'])){
    setcookie("cRows",$_POST['rows'],time() + 60*60*24*365,"/");
    $setupRows = $_POST['rows'];
}


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
if (!empty($rvar_lang))			$lang     = translateHtml($rvar_lang);
if (!empty($rvar_color)) $color=translateHtml($rvar_color);
if (!empty($rvar_baseOrder)) $baseOrder=translateHtml($rvar_baseOrder);
if(!empty($_POST['baseOrder'])) $baseOrder=$_POST['baseOrder'];
if (!empty($rvar_cRows)) $cRows=translateHtml($rvar_cRows);
if (!empty($rvar_rows)) $rows=translateHtml($rvar_rows);
if(!isset($baseOrder) || $baseOrder!="ASC") $baseOrder="DESC";
if(!empty($cRows))     $rows = $cRows;
if(empty($rows)) $rows="100";
if(!empty($setupRows)) $rows=$setupRows;
if(empty($lang) && !isset($setupLang) && ereg("ru",$_SERVER['HTTP_ACCEPT_LANGUAGE'])) $lang="ru_1251";


	// Load language
	if(isset($setupLang)) $lang=$setupLang;
	LanguageSetup($lang);
	
	// Colors scheme
	if(isset($setupColor)) $color=$setupColor;
	ColorSetup($color);

	connect_to_db();

	if (!checkpass()) {
	    nopass();
	}
	if (!hasprivilege("access", false)) {
    	    nopass();
	}

	hasprivilege("access", true);

$title=strtr($GUI_LANG['Settings'],$GUI_LANG['UpperCase'],$GUI_LANG['LowerCase']);
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
    menucomplit("settings");
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
	echo "<H1>".$GUI_LANG['Settings'].":</H1>";
?>
<table cellpadding=0 cellspacing=0 border=0>
    <tr>
	<td>
<form method=post>
<?php
	echo $GUI_LANG['Language']."</td><td>&nbsp;<select name=newLang>\n";
		$dir = "../include/lang";
		$dh  = opendir($dir);
		while (false !== ($filename = readdir($dh))) {
		    $files[] = $filename;
		}

		sort($files);

		$CurrentLang[$lang]=" SELECTED";
		while (list(, $filename) = each($files)) {
		    $lang_name = str_replace(".php", "", $filename);
		    if(ereg(".php", $filename)){
			echo "<option value=".$lang_name.$CurrentLang[$lang_name].">".Langname($dir."/".$filename)."</option>\n";
		    }
		}
		    
		echo"</select></td></tr><tr><td>";
	echo $GUI_LANG['ColorScheme'].":</td><td>&nbsp;<select name=newColor>\n";
		$dirc = "../include/colors";
		$dhc  = opendir($dirc);
		while (false !== ($filenameC = readdir($dhc))) {
		    $filesC[] = $filenameC;
		}

		sort($filesC);

		$CurrentColor[$color]=" SELECTED";
		while (list(, $filenameC) = each($filesC)) {
		    $color_name = str_replace(".php", "", $filenameC);
		    if(ereg(".php", $filenameC)){
			echo "<option value=".$color_name.$CurrentColor[$color_name].">".ColorName($dirc."/".$filenameC)."</option>\n";
		    }
		}
		    
		echo"</select></td></tr><tr><td>";
?>
<?php
	$CurrentOrder['ASC']=$CurrentOrder['DESC']=''; // init
    $CurrentOrder[$baseOrder]=" SELECTED";

    echo $GUI_LANG['SortingByDefault'];

?>:</td><td>&nbsp;<select name=baseOrder>
	<option value="DESC"<?php echo $CurrentOrder['DESC']; ?>><?php echo $GUI_LANG['OnDecrease']; ?></option>
	<option value="ASC"<?php echo $CurrentOrder['ASC']; ?>><?php echo $GUI_LANG['OnIncrease']; ?></option>
    </select></td></tr><tr><td>
<?php echo $GUI_LANG['RowsOnThePage']; ?></td><td>&nbsp;<input TYPE=Text size=5 name=rows maxlength=10 tabindex=1 VALUE=<?php echo $rows; ?>><br>
	</td>
    </tr>
</table>
<br><br><input type=submit class=submit>
</form>                        
<?php
    include("../include/set/footer.html");
?>
