<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */
 
require "./common.inc.php";

$CLASS["template"]->set_templatefiles(array(
    "admin_license" => "admin_license.html"
));
$admin_license = $CLASS["template"]->pre_parse("admin_license");
no_cache_header();
eval("echo \"$admin_license\";");

?>