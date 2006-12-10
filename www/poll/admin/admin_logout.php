<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

require "./common.inc.php";

srand((double)microtime()*1000000);
$new_session = md5 (uniqid (rand()));
$CLASS["db"]->query("UPDATE $POLLTBL[poll_user] SET session='$new_session' WHERE session='$session'");
$CLASS["template"]->set_templatefiles(array(
    "login" => "admin_login.html"
));
$message = $lang['FormEnter'];
$poll_login = $CLASS["template"]->pre_parse("login");
no_cache_header();
eval("echo \"$poll_login\";");

?>