<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

require "./common.inc.php";
require "../include/class_poll.php";
require "../include/class_pollcomment.php";

$include_path = dirname(__FILE__);
$include_path = dirname($include_path);

$CLASS["preview"] = new pollcomment();
$preview_poll_id = $CLASS["preview"]->get_latest_poll_id();
$CLASS["preview"]->include_path = $include_path;
$CLASS["preview"]->set_template_set("$poll_tplset");
$CLASS["preview"]->form_forward = "#";

switch ($tpl_type) {

    case "display":
        $preview = $CLASS["preview"]->display_poll($preview_poll_id);
        break;

    case "result":
        $preview = $CLASS["preview"]->view_poll_result($preview_poll_id);
        break;

    case "comment":
        $preview = $CLASS["preview"]->poll_form($preview_poll_id);
        break;

    default:
        $preview = '';
}

$preview = str_replace("<form method=\"post\"", "<form method=\"post\" onsubmit=\"return false;\"",$preview);
$preview = str_replace("javascript:void(","#",$preview);
$CLASS["template"]->set_templatefiles(array(
    "admin_preview" => "admin_preview.html"
));
$admin_preview = $CLASS["template"]->pre_parse("admin_preview");
no_cache_header();
eval("echo \"$admin_preview\";");

?>