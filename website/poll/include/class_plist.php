<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

class plist extends pollcomment {

    var $poll_list_html;
    var $plist_data;
    var $list_index;
    var $list_page_html;
    var $poll_records;

    function plist() {
        global $HTTP_GET_VARS, $HTTP_POST_VARS;
        $this->poll_list_html = array();
        $this->plist_data = array();
        $this->list_page_html = '';
        $this->poll_records = '';        
        $this->list_index = (isset($HTTP_GET_VARS['l_page'])) ? trim($HTTP_GET_VARS['l_page']) : 0;
        $this->list_index = (isset($HTTP_POST_VARS['l_page'])) ? trim($HTTP_POST_VARS['l_page']) : $this->list_index;
        if (empty($this->list_index) || $this->list_index<0) {
            $this->list_index = 0;
        }
        $this->pollcomment();
    }

    function get_poll_list() {
        if (sizeof($this->plist_data)<1) {
            $record = ($this->poll_records>0) ? $this->poll_records : $this->pollvars['polls_pp'];
            $this->db->query("SELECT * FROM ".$this->tbl['poll_index']." WHERE (status < '2') ORDER BY poll_id DESC LIMIT ".$this->list_index.",$record");
            $total_polls = $this->db->num_rows($this->db->result);
            if ($total_polls>0) {
                for ($i=0; $i<$record; $i++) {
                    if ($this->db->fetch_array($this->db->result)) {
                        $poll_id_arr[] = $this->db->record['poll_id'];
                        $question_arr[] = $this->db->record['question'];
                        $timestamp_arr[] = $this->db->record['timestamp'];
                        $exp_time_arr[] = $this->db->record['exp_time'];
                        $expire_arr[] = $this->db->record['expire'];
                        $comments_arr[] = $this->db->record['comments'];
                    } else {
                        break;
                    }
                }
                $this->plist_data['poll_id'] = $poll_id_arr;
                $this->plist_data['question'] = $question_arr;
                $this->plist_data['timestamp'] = $timestamp_arr;
                $this->plist_data['exp_time'] = $exp_time_arr;
                $this->plist_data['expire'] = $expire_arr;
                $this->plist_data['comments'] = $comments_arr;
            } else {
                $this->plist_data = array();
            }
        }
        return $this->plist_data;
    }

    function view_poll_list() {
        global $HTTP_SERVER_VARS;
        $PHP_SELF = $HTTP_SERVER_VARS['PHP_SELF'];
        if (!isset($this->poll_list_html[$this->comment_tpl])) {            
            $row = $this->db->fetch_array($this->db->query("SELECT template FROM ".$this->tbl['poll_tpl']." WHERE (title = '".$this->comment_tpl."' and tplset_id='0')"));            
            $row['template'] = ereg_replace("\"", "\\\"", $row['template']);
            $list_html = '';
            if (sizeof($this->plist_data<1)) {
                $this->get_poll_list();
            }
            if (sizeof($this->plist_data>0)) {
                for ($i=0;$i<sizeof($this->plist_data['poll_id']);$i++) {
                    $data['timestamp'] = date($this->date_format,$this->plist_data['timestamp'][$i]+$this->pollvars['time_offset']*3600);
                    $data['exp_time'] = date($this->date_format,$this->plist_data['exp_time'][$i]+$this->pollvars['time_offset']*3600);                    
                    $data['poll_id'] = $this->plist_data['poll_id'][$i];
                    $data['question'] = $this->plist_data['question'][$i];
                    $data['comments'] = $this->plist_data['comments'][$i];
                    $data['expire'] = $this->plist_data['expire'][$i];
                    eval("\$list_html .= \"$row[template]\";");
                }
                $this->poll_list_html[$this->comment_tpl] = $list_html;
            } else {
                $this->poll_list_html[$this->comment_tpl] = '';
            }
        }
        return $this->poll_list_html[$this->comment_tpl];
    }

    function set_polls_per_page($records) {
        if (is_integer($records) && $records>0) {
            $this->poll_records = $records;
            return true;
        } else {
            return false;
        }
    }

    function get_total_polls() {
        $this->db->fetch_array($this->db->query("SELECT COUNT(*) AS total FROM ".$this->tbl['poll_index']." WHERE (status < '2')"));
        return $this->db->record["total"];      
    }

    function get_list_pages($max_pages=10, $separate=" | ") {
        if (empty($this->list_page_html)) {
            $record = ($this->poll_records>0) ? $this->poll_records : $this->pollvars['polls_pp'];
            $total_polls = $this->get_total_polls();
            if ($total_polls>0) {
                $this->list_page_html = $this->get_pages($total_polls, $this->list_index, $record, "l_page", $max_pages, $separate);
            } else {
                $this->list_page_html = '';
            }
        }
        return $this->list_page_html;        
    }
    
}

?>