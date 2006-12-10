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
    $select_field = "<select name=\"poll_tplset\" class=\"select\">\n<option value=\"\" selected>$lang[Templates]</option>\n";
    while ($row = $CLASS["db"]->fetch_array($CLASS["db"]->result)) {
        $select_field .= "<option value=\"$row[tplset_id]\">$row[tplset_name]</option>\n";
    }
    $select_field .= "</select>\n";
    return $select_field;
}

function is_valid_tplset_id($poll_tplset='') {
    global $CLASS, $POLLTBL;    
    $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_tplset] where (tplset_id = '$poll_tplset')"));
    if (!$CLASS["db"]->record) {
        $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_tplset] ORDER BY tplset_id asc"));
        return (!$CLASS["db"]->record) ? false : $CLASS["db"]->record['tplset_id'];
    }
    return $CLASS["db"]->record['tplset_id'];
}

function update_tpl($poll_tplset) {
    global $CLASS, $tpl, $POLLTBL; 
    if (is_array($tpl)) {
        reset ($tpl);
        while (list($name, $value) = each($tpl)) {
            if (!get_magic_quotes_gpc()) {
                $value = addslashes($value);
            }
            $CLASS["db"]->query("UPDATE $POLLTBL[poll_tpl] set template='$value' where (tplset_id = '$poll_tplset' and tpl_id='$name')");
        }
    }
}

function get_tplset_name($poll_tplset) {
    global $CLASS, $POLLTBL;
    $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT tplset_name FROM $POLLTBL[poll_tplset] where (tplset_id = '$poll_tplset')"));
    return (!$CLASS["db"]->record) ? false : $CLASS["db"]->record['tplset_name'];
}


if (!isset($action)) {
    $action ='';
}

if ($action=="delete" and isset($poll_tplset)) {
    $CLASS["db"]->query("DELETE FROM $POLLTBL[poll_tpl] where (tplset_id = '$poll_tplset')");
    $CLASS["db"]->query("DELETE FROM $POLLTBL[poll_tplset] where (tplset_id = '$poll_tplset')");
}

if ($action=="$lang[tpl_save]" and isset($tplset)) {
    update_tpl($tplset);
    $poll_tplset = $tplset;
} elseif (!isset($poll_tplset)) {
    $poll_tplset ='';
}

$poll_tplset = is_valid_tplset_id($poll_tplset);
$poll_tplset_name = get_tplset_name ($poll_tplset);

if (!$poll_tplset) {
    $tpl_type = "new";
    $message = $lang['tpl_new'];
} else {
    $CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_tpl] WHERE tplset_id='$poll_tplset'");
    while ($tpl = $CLASS["db"]->fetch_array($CLASS["db"]->result)) {
        $poll_tpl[$tpl['title']] = htmlspecialchars($tpl['template']);
        $poll_tpl_id[$tpl['title']] = $tpl['tpl_id'];
    }
}

if (!isset($tpl_type)) {
    $tpl_type = "display";
}

$select_field = get_tplset();

$CLASS["template"]->set_templatefiles(array(
    "admin_templates" => "admin_tpl_".$tpl_type.".html"
));
$admin_templates = $CLASS["template"]->pre_parse("admin_templates");
no_cache_header();
eval("echo \"$admin_templates\";");

?>