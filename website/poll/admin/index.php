<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

require "./common.inc.php";

function delete_poll($poll_id) {
    global $CLASS, $POLLTBL;
    $CLASS["db"]->query("DELETE FROM $POLLTBL[poll_data] where (poll_id = '$poll_id')");
    $CLASS["db"]->query("DELETE FROM $POLLTBL[poll_index] where (poll_id = '$poll_id')");
    $CLASS["db"]->query("DELETE FROM $POLLTBL[poll_log] where (poll_id = '$poll_id')");
    $CLASS["db"]->query("DELETE FROM $POLLTBL[poll_comment] where (poll_id = '$poll_id')");
}

function poll_index() {
    global $CLASS, $auth, $pollvars, $entry, $lang, $weekday, $months, $POLLTBL;
    if(!isset($entry)) {
        $entry = 0;
    }
    $CLASS["db"]->fetch_array($CLASS["db"]->query("select count(*) as total from $POLLTBL[poll_index]"));
    $total = $CLASS["db"]->record['total'];
    $time_offset = $pollvars["time_offset"]*3600;
    list($wday,$mday,$month,$year,$hour,$minutes) = split("( )",date("w j n Y H i",time()+$time_offset));
    $newdate = "$weekday[$wday], $mday ".$months[$month-1]." $year $hour:$minutes";
    $next_page = $entry+$pollvars['polls_pp'];
    $prev_page = $entry-$pollvars['polls_pp'];
    $navigation ='';
    if ($prev_page >= 0) {
        $navigation = "  <img src=\"$pollvars[base_gif]/back.gif\" width=\"16\" height=\"14\">&nbsp;<a href=\"$pollvars[SELF]?session=$auth[session]&uid=$auth[uid]&entry=$prev_page\">$lang[NavPrev]</a>\n";
    }
    if ($next_page < $total) {
        $navigation = $navigation. "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$pollvars[SELF]?session=$auth[session]&uid=$auth[uid]&entry=$next_page\">$lang[NavNext]</a>&nbsp;<img src=\"$pollvars[base_gif]/next.gif\" width=\"16\" height=\"14\">\n";
    }
    $CLASS["template"]->set_templatefiles(array(
        "index" => "admin_index.html",
        "index_tr" => "admin_index_tr.html"
    ));
    $CLASS["template"]->register_vars("index_tr",array(
        "poll_id" => "\$row[poll_id]"
    ));
    $index_tr = $CLASS["template"]->pre_parse("index_tr");
    $results = $CLASS["db"]->query("select * from $POLLTBL[poll_index] order by poll_id desc limit $entry, $pollvars[polls_pp]");
    $admin_index_tr = '';
    while ($row = $CLASS["db"]->fetch_array($results)) {
        $question = htmlspecialchars($row['question']);
        $date = date("j-M-Y",$row['timestamp']+$time_offset);
        if ($row['expire']==0) {
            $exp_date = "<font color=\"#0000FF\">$lang[IndexNever]</font>";
        } else {
            $exp_date = (time()>$row['exp_time']) ? "<font color=\"#FF6600\">$lang[IndexExpire]</font>" : date("j-M-Y",$row['exp_time']+$time_offset)." (<font color=\"#FF0000\">".round(($row["exp_time"]-time())/86400)."</font>)";
        }
        $days = (int) ((time()-$row['timestamp']+$time_offset)/86400);
        if ($row['status'] == 1) {
            $image = "$pollvars[base_gif]/folder.gif";
            $alt = "$lang[EditOn]";
        } elseif ($row['status'] == 2) {
            $image = "$pollvars[base_gif]/hidden.gif";
            $alt = "$lang[EditHide]";
        } else {
            $image = "$pollvars[base_gif]/lock.gif";
            $alt = "$lang[EditOff]";
        }
        $alt = htmlspecialchars($alt);
        $image2 = ($row['logging'] == 1) ? "$pollvars[base_gif]/log.gif" : "$pollvars[base_gif]/log_off.gif";
        $image3 = ($row['comments'] == 1) ? "$pollvars[base_gif]/reply.gif" : "$pollvars[base_gif]/co_dis.gif";
        $image4 = ($row['status'] == 2) ? "$pollvars[base_gif]/text_off.gif" : "$pollvars[base_gif]/text.gif";
        eval("\$admin_index_tr .= \"$index_tr\";");
    }
    $CLASS["template"]->register_vars("index", array(
        "poll_user" => $CLASS["db"]->db['user'],
        "poll_host" => $CLASS["db"]->db['host']
    ));
    $admin_index = $CLASS["template"]->pre_parse("index");
    eval("echo \"$admin_index\";");
}

function poll_new($message) {
    global $CLASS, $auth, $pollvars, $lang, $color_array;
    $source_array = array(
        "aqua","blue","brown","darkgreen","gold","green","grey","orange","pink","purple","red","yellow"
    );
    for ($i=0,$java_script='';$i<sizeof($source_array); $i++) {
        $java_script .= "$source_array[$i] = new Image(); $source_array[$i].src = \"$pollvars[base_gif]/$source_array[$i].gif\";\n";
    }
    for ($i=1,$poll_options=''; $i < $pollvars['def_options']+1; $i++) {
        $poll_options .= "        <tr>
                 <td width=\"25%\" class=\"td1\">$lang[NewOption] $i</td>
                 <td width=\"40%\">
                   <input type=\"text\" name=\"option_id[$i]\" size=\"38\" class=\"input\" maxlength=\"100\">
                 </td>
                 <td class=\"td2\" width=\"10%\">
                   <select class=\"select\" name=\"color[$i]\" onChange=\"javascript:ChangeBar(options[selectedIndex].value,$i)\">
                    <option value=\"blank\">---</option>\n";
        for ($j=0; $j <sizeof($source_array); $j++) {
            $poll_options .= "<option value=\"$source_array[$j]\">$color_array[$j]</option>\n";
        }
        $poll_options .= "       </select></td>
            <td width=\"25%\" align=\"left\"><img src=\"$pollvars[base_gif]/blank.gif\" name=\"bar$i\" width=\"35\" height=\"12\"></td>
            </tr>\n";
    }
    $CLASS["template"]->set_templatefiles(array(
        "admin_new" => "admin_new.html"
    ));
    $admin_new = $poll_login = $CLASS["template"]->pre_parse("admin_new");
    eval("echo \"$admin_new\";");
}

function create_poll() {
    global $CLASS, $POLLTBL;
    global $logging, $expire, $exp_time, $status, $comments;
    global $option_id, $question, $color;
    $timestamp = time();
    if (!isset($expire)) {
        $expire=1;
    }
    if (!isset($comments)) {
        $comments=0;
    }
    if (!isset($exp_time)) {
        $exp_time=$timestamp;
    } else {
        $exp_time=$timestamp+$exp_time*86400;
    }
    if (!get_magic_quotes_gpc()) {
        $question = addslashes($question);
    }
    $CLASS["db"]->query("INSERT INTO $POLLTBL[poll_index] (question,timestamp,status,logging,exp_time,expire,comments) VALUES ('$question','$timestamp','$status','$logging','$exp_time','$expire','$comments')");
    $sql_result = $CLASS["db"]->query("SELECT poll_id FROM $POLLTBL[poll_index] WHERE timestamp=$timestamp");
    $CLASS["db"]->fetch_array($sql_result);
    $poll_id = $CLASS["db"]->record['poll_id'];
    for($i=1; $i <= sizeof($option_id); $i++) {
        $option_id[$i] = trim($option_id[$i]);
        if (!empty($option_id[$i])) {
            if (!get_magic_quotes_gpc()) {
                $option_id[$i] = addslashes($option_id[$i]);
            }
            $CLASS["db"]->query("INSERT INTO $POLLTBL[poll_data] (poll_id, option_id, option_text, color, votes) VALUES('$poll_id', '$i', '$option_id[$i]','$color[$i]',0)");
        }
    }
}

if (!isset($action)) {
    $action='';
}

no_cache_header();

switch ($action) {

    case "new":
        $message = $lang["NewTitle"];
        poll_new("$message");
        break;

    case "show":
        poll_index();
        break;

    case "delete":
        if (isset($id)) {
            delete_poll($id);
        }
        poll_index();
        break;

    case "create":
        $question = trim($question);
        if (!empty($question)) {
            create_poll();
            poll_index();
        } else {
            $message = $lang["EditMis"];
            poll_new("$message");
        }
        break;

    default:
        poll_index();
        break;
}

?>