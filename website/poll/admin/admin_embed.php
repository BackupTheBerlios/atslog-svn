<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */
 
require "./common.inc.php";

$record = $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_index] WHERE (poll_id = '$poll_id')"));
if (!isset($poll_id) || ($record['status']==2)) {
    $redirect = "index.php?session=$auth[session]&uid=$auth[uid]";
    header ("Location: $redirect");
    exit();
}
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
$question = htmlspecialchars($record['question']);
$CLASS["db"]->free_result($CLASS["db"]->result);
$CLASS["template"]->set_templatefiles(array(
    "admin_embed" => "admin_embed.html"
));
$admin_embed = $CLASS["template"]->pre_parse("admin_embed");
no_cache_header();
eval("echo \"$admin_embed\";");

?>