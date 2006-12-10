<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

require "../include/config.inc.php";
require "../include/$POLLDB[class]";
require "../include/class_session.php";
require "../include/class_template.php";

function no_cache_header() {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
}

if (!isset($PHP_SELF)) {
    $PHP_SELF = $HTTP_SERVER_VARS["PHP_SELF"];
    if (isset($HTTP_GET_VARS)) {
        while (list($name, $value)=each($HTTP_GET_VARS)) {
            $$name=$value;
        }
    }
    if (isset($HTTP_POST_VARS)) {
        while (list($name, $value)=each($HTTP_POST_VARS)) {
            $$name=$value;
        }
    }
    if (isset($HTTP_COOKIE_VARS)){
        while (list($name, $value)=each($HTTP_COOKIE_VARS)) {
            $$name=$value;
        }
    }
}

$CLASS["db"] = new polldb_sql;
$CLASS["db"]->connect();
$pollvars = $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_config]"));
$CLASS["db"]->free_result($CLASS["db"]->result);
$pollvars['SELF'] = basename($PHP_SELF);
unset($lang);
if (file_exists("../lang/$pollvars[lang]")) {
    include ("../lang/$pollvars[lang]");
} else {
    include ("../lang/english.php");
}
$CLASS["template"] = new poll_template();
$CLASS["template"]->set_rootdir("./templates");
$CLASS["session"] = new poll_session();
$CLASS["session"]->db = $CLASS["db"];

$auth = $CLASS["session"]->check_session_id();

if (!$auth) {
    $message = (isset($username) || isset($password)) ? $lang['FormWrong'] : $lang['FormEnter'];
    $CLASS["template"]->set_templatefiles(array(
        "login" => "admin_login.html"
    ));
    $poll_login = $CLASS["template"]->pre_parse("login");
    no_cache_header();
    eval("echo \"$poll_login\";");
    exit();

}

?>