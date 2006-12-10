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
if ($action=="reset" and isset($poll_id)) {
    $CLASS["db"]->query("DELETE FROM $POLLTBL[poll_log] where (poll_id = '$poll_id')");
}

$row = $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_index] WHERE (poll_id = '$poll_id')"));
$logging = $row["logging"];
$CLASS["db"]->free_result($CLASS["db"]->result);
$time_offset = $pollvars["time_offset"]*3600;
$poll_sum = $CLASS["db"]->fetch_array($CLASS["db"]->query("SELECT SUM(votes) AS total FROM $POLLTBL[poll_data] WHERE (poll_id = '$poll_id')"));
$CLASS["db"]->free_result($CLASS["db"]->result);
list($wday,$mday,$month,$year,$hour,$minutes) = split("( )",date("w j n Y H i",$row['timestamp']+$time_offset));
$newdate = "$weekday[$wday], $mday ".$months[$month-1]." $year $hour:$minutes";

$CLASS["template"]->set_templatefiles(array(
    "admin_stats" => "admin_stats.html"
));
$poll_stats = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">";
$hours = (int) ((time()-$row['timestamp']+$time_offset)/3600);
$days = (int) ($hours/24);
$remain = $hours%24;
$question = htmlspecialchars($row['question']);
$poll_sum_total = $poll_sum["total"];
$result = $CLASS["db"]->query("select * from $POLLTBL[poll_data] where (poll_id = '$poll_id') order by option_id asc");
while ($data = $CLASS["db"]->fetch_array($result)) {
    $percent = ($poll_sum['total'] == 0) ? "0%" : sprintf("%.2f",($data['votes']*100/$poll_sum['total']))."%";
    $perday = ($days>0) ? sprintf("%.1f",($data["votes"]/$days)) : $data["votes"];
    $poll_stats .= "<tr>
                <td colspan=\"4\" class=\"td2\"><b>$lang[NewOption] $data[option_id]: $data[option_text]</b></td>
              </tr>
              <tr>
                <td colspan=\"2\" class=\"td2\">- $lang[SetVotes]: <font color=\"#CC0000\">$data[votes]</font> ($percent)</td>
                <td colspan=\"2\" class=\"td2\">- <font color=\"#0000FF\">$perday</font> $lang[StatDay]</td>
              </tr>\n";
    if ($logging == 1) {
        $log_result = $CLASS["db"]->query("select * from $POLLTBL[poll_log] where (poll_id = '$poll_id' and option_id = '$data[option_id]')");
        $row = $CLASS["db"]->num_rows($log_result);
        if ($row != 0) {
            $poll_stats .= "              <tr bgcolor=\"#CC9999\" class=\"td2\">
                  <td width=\"15%\" class=\"td2\">$lang[IndexDate]</td>
                  <td width=\"13%\" class=\"td2\">IP</td>
                  <td width=\"22%\" class=\"td2\">Host</td>
                  <td width=\"50%\" class=\"td2\">Browser</td>
                </tr>\n";
            while ($log_data = $CLASS["db"]->fetch_array($log_result)) {
                $poll_stats .= "        <tr>
                  <td width=\"15%\" class=\"td2\">".date("j-M-Y H:i",$log_data['timestamp']+$time_offset)."</td>
                  <td width=\"13%\" class=\"td2\">$log_data[ip_addr]</td>
                  <td width=\"22%\" class=\"td2\">$log_data[host]</td>
                  <td width=\"50%\" class=\"td2\">$log_data[agent]</td>
                </tr>\n";
            }
        }
    }
}
$poll_stats .= "</table>\n";
$admin_stats = $CLASS["template"]->pre_parse("admin_stats");
no_cache_header();
eval("echo \"$admin_stats\";");

?>