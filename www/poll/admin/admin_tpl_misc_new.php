<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

require "./common.inc.php";

function get_tpl() {
    global $CLASS, $lang, $POLLTBL;
    $CLASS["db"]->query("SELECT tpl_id,title FROM $POLLTBL[poll_tpl] WHERE (tplset_id = '0') ORDER BY tpl_id asc");
    $select_field = "<select name=\"poll_tpl_id_select\" class=\"select\">\n<option value=\"\" selected>$lang[Templates]</option>\n";
    while ($row = $CLASS["db"]->fetch_array($CLASS["db"]->result)) {
        $select_field .= "<option value=\"$row[tpl_id]\">$row[title]</option>\n";
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

function new_tpl($new_tplname) {
    global $CLASS, $POLLTBL, $new_poll_tpl;
    if (!isset($new_poll_tpl)) {
        $new_poll_tpl = '';
    }
    if (!get_magic_quotes_gpc()) {
        $new_poll_tpl = addslashes($new_poll_tpl);
    }
    $now = date("Y-m-d H:i:s",time());
    $CLASS["db"]->query("INSERT INTO $POLLTBL[poll_tpl] (tplset_id,title,template) VALUES ('0','$new_tplname','$new_poll_tpl')");
    return true;
}


if (!isset($new_tplname)) {
    $new_tplname = '';
} else {
    $new_tplname = trim(strip_bad_chars($new_tplname));
}

if ((empty($new_tplname) && isset($action)) || ($new_tplname == "display_head" || $new_tplname == "display_loop" || $new_tplname == "result_loop" || $new_tplname == "result_head") && isset($action)) {
    $message = $lang['tpl_bad'];
} elseif (!empty($new_tplname) && isset($action)) {
    $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT title FROM $POLLTBL[poll_tpl] WHERE title='$new_tplname' and tplset_id='0'"));
    if ($CLASS["db"]->record) {
        $message = $lang['tpl_exist'];
    } else {
        new_tpl($new_tplname);
        $message = $lang['tpl_succes'];
    }
} else {
    $message = "Add a new template";
}

$select_field = get_tpl();

$CLASS["template"]->set_templatefiles(array(
    "admin_tpl_misc_new" => "admin_tpl_misc_new.html"
));
$admin_templates = $CLASS["template"]->pre_parse("admin_tpl_misc_new");
no_cache_header();
eval("echo \"$admin_templates\";");

?>