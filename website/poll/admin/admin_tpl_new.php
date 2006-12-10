<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

require "./common.inc.php";

function get_tplset() {
    global $CLASS, $lang, $POLLTBL;
    $CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_tplset] ORDER BY tplset_id asc");
    $select_field = "<select name=\"poll_tplset\" class=\"select\">\n<option selected>$lang[Templates]</option>\n";
    while ($row = $CLASS["db"]->fetch_array($CLASS["db"]->result)) {
        $select_field .= "<option value=\"$row[tplset_id]\">$row[tplset_name]</option>\n";
    }
    $select_field .= "</select>\n";
    return $select_field;
}

function strip_bad_chars($strg) {
    $bad_chars = array("/","\\","\"","'","?","#","+","~","<",">","*","|",":");
    for($i=0; $i<sizeof($bad_chars); $i++) {
        $strg = str_replace($bad_chars[$i],"",$strg);
    }
    return $strg;
}

function new_tplset($new_tplsetname) {
    global $CLASS, $POLLTBL;
    $now = date("Y-m-d H:i:s",time());
    $tpl_array = array("display_head","display_loop","display_foot","result_head","result_loop","result_foot","comment");
    $CLASS["db"]->query("INSERT INTO $POLLTBL[poll_tplset] (tplset_name,created) VALUES ('$new_tplsetname','$now')");
    $CLASS["db"]->fetch_array($CLASS["db"]->query("select max(tplset_id) as tplset_id from $POLLTBL[poll_tplset]"));
    $new_tpl_id = $CLASS["db"]->record["tplset_id"];
    for ($i=0; $i<sizeof($tpl_array); $i++) {
        $CLASS["db"]->query("INSERT INTO $POLLTBL[poll_tpl] (tplset_id,title,template) VALUES ('$new_tpl_id','$tpl_array[$i]','')");
    }
}

if (!isset($new_tplsetname)) {
    $new_tplsetname = '';
} else {
    $new_tplsetname = trim(strip_bad_chars($new_tplsetname));
}

if (empty($new_tplsetname) && isset($action)) {
    $message = $lang['tpl_bad'];
} elseif (!empty($new_tplsetname) && isset($action)) {
    $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_tplset] WHERE tplset_name='$new_tplsetname'"));
    if ($CLASS["db"]->record) {
        $message = $lang['tpl_exist'];
    } else {
        new_tplset($new_tplsetname);
        $message = $lang['tpl_succes'];
    }
} else {
    $message = $lang['tpl_new'];
}

$select_field = get_tplset();

$CLASS["template"]->set_templatefiles(array(
    "admin_tpl_new" => "admin_tpl_new.html"
));
$admin_templates = $CLASS["template"]->pre_parse("admin_tpl_new");
no_cache_header();
eval("echo \"$admin_templates\";");

?>