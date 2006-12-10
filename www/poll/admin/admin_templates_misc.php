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

function get_valid_tpl_id($poll_tpl='') {
    global $CLASS, $POLLTBL;    
    $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT tpl_id FROM $POLLTBL[poll_tpl] where (tpl_id = '$poll_tpl')"));
    if (!$CLASS["db"]->record) {
        $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT tpl_id FROM $POLLTBL[poll_tpl] WHERE (tplset_id = '0') ORDER BY tpl_id asc"));
        return (!$CLASS["db"]->record) ? false : $CLASS["db"]->record['tpl_id'];
    }
    return $CLASS["db"]->record['tpl_id'];
}

function update_tpl($poll_tpl_id) {
    global $CLASS, $poll_tpl, $POLLTBL; 
    if (isset($poll_tpl)) {
        if (!get_magic_quotes_gpc()) {
            $poll_tpl = addslashes($poll_tpl);
        }
        $CLASS["db"]->query("UPDATE $POLLTBL[poll_tpl] set template='$poll_tpl' where (tplset_id = '0' and tpl_id='$poll_tpl_id')");
    }
}

if (!isset($action)) {
    $action ='';
}

if ($action=="delete" and isset($poll_tpl_id)) {
    $CLASS["db"]->query("DELETE FROM $POLLTBL[poll_tpl] where (tpl_id = '$poll_tpl_id' and tplset_id='0')");
}

if ($action=="$lang[tpl_save]" and isset($poll_tpl)) {
    update_tpl($poll_tpl_id);
}

$tpl_id = (isset($poll_tpl_id_select)) ? $poll_tpl_id_select : '';
if (empty($tpl_id)) {
    $tpl_id = (isset($poll_tpl_id)) ? $poll_tpl_id : '';
}

$poll_tpl_id = get_valid_tpl_id($tpl_id);

if (!$poll_tpl_id) {
    $tpl_type = "new";
    $message = $lang['tpl_new'];
} else {
    $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_tpl] WHERE tpl_id='$poll_tpl_id'"));
    $tpl = $CLASS["db"]->record;
    $tpl['template'] = htmlspecialchars($CLASS["db"]->record['template']);
}

if (!isset($tpl_type)) {
    $tpl_type = "edit";
}

$select_field = get_tpl();

$CLASS["template"]->set_templatefiles(array(
    "admin_templates" => "admin_tpl_misc_".$tpl_type.".html"
));
$admin_templates = $CLASS["template"]->pre_parse("admin_templates");
no_cache_header();
eval("echo \"$admin_templates\";");

?>