<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */
 
require "./common.inc.php";

$source_array = array(
    "aqua","blue","brown","darkgreen","gold","green","grey","orange","pink","purple","red","yellow"
);

function add_options($poll_id,$last_id) {
    global $CLASS, $POLLTBL;
    global $option_id, $color;
    for($i=$last_id; $i < $last_id+10; $i++) {
        $option_id["$i"] = trim($option_id["$i"]);
        if (!empty($option_id["$i"])) {
            if (!get_magic_quotes_gpc()) {
                $option_id["$i"] = addslashes($option_id["$i"]);
            }
            $CLASS["db"]->query("INSERT INTO $POLLTBL[poll_data] (poll_id, option_id, option_text, color, votes) VALUES('$poll_id', '$i', '$option_id[$i]','$color[$i]',0)");
            $added = 1;
        }
    }
    return (isset($added)) ? "EditOk" : "EditNo";
}

function save($poll_id) {
    global $CLASS, $POLLTBL;
    global $option_id, $votes, $color, $status, $logging, $question, $exp_time, $expire, $comments;
    if (!isset($expire)) {
        $expire=1;
    }
    if (!isset($comments)) {
        $comments=0;
    }
    $exp_time=time()+$exp_time*86400;
    $question = trim($question);
    if (!empty($question)) {
        if (!get_magic_quotes_gpc()) {
            $question = addslashes($question);
        }
        $CLASS["db"]->query("UPDATE $POLLTBL[poll_index] set question='$question', status='$status', logging='$logging', exp_time='$exp_time', expire='$expire', comments='$comments' where (poll_id = '$poll_id')");
        $CLASS["db"]->query("select max(option_id) as max_option from $POLLTBL[poll_data] where (poll_id = '$poll_id')");
        $data = $CLASS["db"]->fetch_array($CLASS["db"]->result);
        for($i=1; $i <= $data["max_option"]; $i++) {
            if (!isset($option_id["$i"])) {
                continue;
            }
            $option_id["$i"] = trim($option_id["$i"]);
            if (!empty($option_id[$i])) {
                if (!eregi("^[0-9]+$", $votes[$i])) {
                    $votes[$i] = 0;
                }
                if (!get_magic_quotes_gpc()) {
                    $option_id[$i] = addslashes($option_id[$i]);
                }
                $CLASS["db"]->query("UPDATE $POLLTBL[poll_data] set option_text='$option_id[$i]', color='$color[$i]', votes='$votes[$i]' where (poll_id = '$poll_id' and option_id = '$i')");
            } elseif (sizeof($option_id) > 2) {
                $CLASS["db"]->query("DELETE FROM $POLLTBL[poll_data] where (poll_id = '$poll_id' and option_id = '$i')");
            } else {
                $message = "EditOp";
            }
        }
        $message = "Updated";
    } else {
        $message = "NewNoQue";
    }
    return $message;
}

function create_javascript_array() {
    global $pollvars, $source_array;
    for ($i=0, $java_script=''; $i<sizeof($source_array); $i++) {
        $java_script .= "$source_array[$i] = new Image(); $source_array[$i].src = \"$pollvars[base_gif]/$source_array[$i].gif\";\n";
    }    
    return $java_script;
}

function poll_extend($poll_id) {
    global $CLASS, $POLLTBL, $source_array, $color_array, $lang, $pollvars, $auth;
    $row = $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT question as question FROM $POLLTBL[poll_index] WHERE (poll_id = '$poll_id')"));
    $question = htmlspecialchars($row['question']);
    $CLASS["db"]->free_result($CLASS["db"]->result);
    $data = $CLASS["db"]->fetch_array($CLASS["db"]->query("select max(option_id) as option_id from $POLLTBL[poll_data] where (poll_id = '$poll_id')"));
    $CLASS["db"]->free_result($CLASS["db"]->result);
    $CLASS["template"]->set_templatefiles(array(
        "admin_options" => "admin_options.html"
    ));
    $java_script = create_javascript_array();
    $poll_options = '';
    $data["option_id"] += 1;
    $i = $data["option_id"];
    $end = $i+$pollvars['def_options'];
    for ($i; $i < $end; $i++) {
        $poll_options .= "    <tr>
                <td width=\"15%\" class=\"td1\">$lang[NewOption] $i</td>
                <td width=\"48%\">
                  <input type=\"text\" name=\"option_id[$i]\" size=\"38\" class=\"input\" maxlength=\"100\">
                </td>
                <td width=\"12%\" class=\"td2\">
                  <select class=\"select\" name=\"color[$i]\" onChange=\"javascript:ChangeBar(options[selectedIndex].value,$i)\">
                    <option value=\"blank\">---</option>\n";
        for ($j=0; $j <sizeof($source_array); $j++) {
            $poll_options .= "<option value=\"$source_array[$j]\">$color_array[$j]</option>\n";
        }
        $poll_options .= "       </select></td>
            <td width=\"20%\"><img src=\"$pollvars[base_gif]/blank.gif\" name=\"bar$i\" width=35 height=12></td>
        </tr>\n";
    }
    $last_option_id = $data["option_id"];
    $admin_options = $CLASS["template"]->pre_parse("admin_options");
    eval("echo \"$admin_options\";");    
}

function poll_edit($poll_id,$message) {
    global $CLASS, $auth, $pollvars, $color_array, $source_array, $lang, $POLLTBL;
    $row = $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_index] WHERE (poll_id = '$poll_id')"));
    $question = htmlspecialchars($row['question']);
    $CLASS["db"]->free_result($CLASS["db"]->result);
    $java_script = create_javascript_array();
    $CLASS["db"]->query("select * from $POLLTBL[poll_data] where (poll_id = '$poll_id') order by option_id asc");
    $i=1;
    $poll_options = '';
    $status_0 = ($row['status'] == 0) ? "selected" : "";
    $status_1 = ($row['status'] == 1) ? "selected" : "";
    $status_2 = ($row['status'] == 2) ? "selected" : "";
    $logging_0 = ($row['logging'] == 0) ? "selected" : "";
    $logging_1 = ($row['logging'] == 1) ? "selected" : "";
    $poll_comments = ($row['comments'] == 1) ? "checked" : "";
    $poll_expire = ($row['expire'] == 0) ? "checked" : "";
    while ($data = $CLASS["db"]->fetch_array($CLASS["db"]->result)) {
        $i++;
        $data["option_text"] = htmlspecialchars($data["option_text"]);
        $poll_options .= "         <tr>
                 <td width=\"20%\" class=\"td1\">$lang[NewOption] $data[option_id]</td>
                 <td width=\"49%\">
                   <input type=\"text\" name=\"option_id[$data[option_id]]\" size=\"35\" class=\"input\" value=\"$data[option_text]\">
                 </td>
                 <td width=\"11%\" class=\"td2\">
                   <input type=\"text\" name=\"votes[$data[option_id]]\" class=\"input2\" size=\"10\" value=\"$data[votes]\">
                 </td>
                 <td width=\"11%\" class=\"td2\">
                  <select name=\"color[$data[option_id]]\" class=\"select\" onChange=\"javascript:ChangeBar(options[selectedIndex].value,$data[option_id])\">
                  <option value=\"blank\">---</option>\n";
        for ($j=0; $j<sizeof($source_array); $j++) {
            if ($data["color"] == $source_array["$j"]) {
                $poll_options .= "<option value=\"$source_array[$j]\" selected>$color_array[$j]</option>\n";
            } else {
                $poll_options .= "<option value=\"$source_array[$j]\">$color_array[$j]</option>\n";
            }
        }
        $poll_options .= "          </select>
                  </td>
                  <td width=\"9%\"><img src=\"$pollvars[base_gif]/$data[color].gif\" name=\"bar$data[option_id]\" width=35 height=12></td>
                </tr>\n";
    }
    $expiration = round (($row['exp_time']-time())/86400);
    if ($expiration<=0) {
        $expiration = 0;
    }
    $timestamp = '';
    $CLASS["template"]->set_templatefiles(array(
        "admin_edit" => "admin_edit.html"
    ));
    $admin_edit = $CLASS["template"]->pre_parse("admin_edit");
    eval("echo \"$admin_edit\";");
}

function is_valid_poll_id($poll_id) {
    global $CLASS, $POLLTBL;
    if ($poll_id>0) {
        $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT poll_id FROM $POLLTBL[poll_index] WHERE poll_id=$poll_id"));
        return ($CLASS["db"]->record['poll_id']) ? true : false;
    } else {
        return false;
    }
}

if (!isset($poll_id) || !is_valid_poll_id($poll_id)) {
    $redirect = "index.php?session=$auth[session]&uid=$auth[uid]";
    header ("Location: $redirect");
    exit();
}

if (!isset($action)) {
    $action='';
}

no_cache_header();

switch ($action) {

    case "save":
        $message = save($poll_id);
        $message = $lang[$message];
        poll_edit($poll_id,"$message");
        break;

    case "extend":
        poll_extend($poll_id);
        break;

    case "add":
        $message = add_options($poll_id,$last_id);
        $message = $lang[$message];
        poll_edit($poll_id,"$message");
        break;

    default:
        $message = $lang["EditText"];
        poll_edit($poll_id,"$message");
}


?>