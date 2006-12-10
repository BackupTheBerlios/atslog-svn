<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

require "./common.inc.php";

function update_user($username,$userpass) {
    global $CLASS, $POLLTBL, $auth;
    $userpass = md5($userpass);
    if ($CLASS["db"]->query("UPDATE $POLLTBL[poll_user] SET username='$username', userpass='$userpass' WHERE session='$auth[session]'")) {
        return "Updated";
    } else {
        return "NoUpdate";
    }
}

if (!isset($action)) {
    $action='';
}

if ($action== "update_pwd") {
    if (!empty($NEWadmin_name)) {
        $username = trim($NEWadmin_name);
        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }
    }
    if (!empty($NEWadmin_pass)) {
        $userpass = trim($NEWadmin_pass);
        if (get_magic_quotes_gpc()) {
            $userpass = stripslashes($userpass);
        }
    }
    $lang_mes = update_user($username,$userpass);
} else {
    $lang_mes = "PwdText";
}

$CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT username FROM $POLLTBL[poll_user] where session='$session'"));

$CLASS["template"]->set_templatefiles(array(
    "admin_password" => "admin_password.html"
));

$message = $lang[$lang_mes];
$username = $CLASS["db"]->record["username"];
$password_settings = $CLASS["template"]->pre_parse("admin_password");
no_cache_header();
eval("echo \"$password_settings\";");

?>