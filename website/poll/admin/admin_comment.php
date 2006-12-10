<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

require "./common.inc.php";

if (!isset($action)) {
    $action='';
}
if ($action=="delete" and isset($mess_id) and isset($poll_id)) {
    $CLASS["db"]->query("DELETE FROM $POLLTBL[poll_comment] where (com_id = '$mess_id' and poll_id='$poll_id')");
}

if(!isset($entry)) {
    $entry = 0;
}
$record = $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT question FROM $POLLTBL[poll_index] WHERE (poll_id = '$poll_id')"));
$question = htmlspecialchars($record['question']);
$CLASS["db"]->free_result($CLASS["db"]->result);
$time_offset = $pollvars["time_offset"]*3600;
$CLASS["db"]->fetch_array($CLASS["db"]->query("select count(*) as total from $POLLTBL[poll_comment] WHERE (poll_id = '$poll_id')"));
$total = $CLASS["db"]->record['total'];
$next_page = $entry+$pollvars["entry_pp"];
$prev_page = $entry-$pollvars["entry_pp"];
$navigation ='';
if ($prev_page >= 0) {
    $navigation = "   <img src=\"$pollvars[base_gif]/back.gif\" width=\"16\" height=\"14\">&nbsp;<a href=\"$pollvars[SELF]?session=$auth[session]&uid=$auth[uid]&poll_id=$poll_id&entry=$prev_page\">$lang[NavPrev]</a>\n";
}
if ($next_page < $total) {
    $navigation = $navigation. " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$pollvars[SELF]?session=$auth[session]&uid=$auth[uid]&poll_id=$poll_id&entry=$next_page\">$lang[NavNext]</a>&nbsp;<img src=\"$pollvars[base_gif]/next.gif\" width=\"16\" height=\"14\">\n";
}
$CLASS["template"]->set_templatefiles(array(
    "admin_comment"    => "admin_comment.html",
    "admin_comment_tr" => "admin_comment_tr.html"
));
$CLASS["template"]->register_vars("admin_comment_tr",array(
    "message" => "\$row[message]",
    "browser" => "\$row[browser]",
    "name"    => "\$row[name]",
    "host"    => "\$row[host]",
    "com_id"  => "\$row[com_id]"
));
$admin_comment_tr = '';
$comment_tr = $CLASS["template"]->pre_parse("admin_comment_tr");
$results = $CLASS["db"]->query("select * from $POLLTBL[poll_comment] WHERE (poll_id = '$poll_id') order by com_id desc limit $entry, $pollvars[entry_pp]");
while ($row = $CLASS["db"]->fetch_array($results)) {
    $date = date("j-M-Y H:i",$row['time']+$time_offset);
    $row['message'] = nl2br($row['message']);
    $email = ($row['email']) ? "<a href=\"mailto:$row[email]\"><img src=\"$pollvars[base_gif]/email.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"$row[email]\"></a>\n" : "";
    if (eregi("Opera",$row['browser'])) {
        $image = "$pollvars[base_gif]/opera.gif";
    } elseif (eregi("MSIE",$row['browser'])) {
        $image = "$pollvars[base_gif]/msie.gif";
    } elseif (eregi("Mozilla",$row['browser'])) {
        $image = "$pollvars[base_gif]/netscape.gif";
    } else {
        $image = "$pollvars[base_gif]/unknown.gif";
    }
    eval("\$admin_comment_tr .= \"$comment_tr\";");
}
$comments = $CLASS["template"]->pre_parse("admin_comment");
no_cache_header();
eval("echo \"$comments\";");

?>