<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */
 
require "./common.inc.php";

$path = dirname(__file__);
$path = dirname("$path");
if (eregi("WIN",PHP_OS)) {
    $path = str_replace("\\","/",$path);
}
if (ereg("^3.",PHP_VERSION)) {
    $include_statement = "include";
} else {
    $version = ereg_replace("([^0-9])", "", PHP_VERSION);
    $version = $version / pow (10, strlen($version) - 1);
    $include_statement = ($version >= 4.02) ? "include_once" : "include";
}
$CLASS["template"]->set_templatefiles(array(
    "admin_help" => "admin_help.html"
));
$admin_help = $CLASS["template"]->pre_parse("admin_help");
no_cache_header();
eval("echo \"$admin_help\";");

?>