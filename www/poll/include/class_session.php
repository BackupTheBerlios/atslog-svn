<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

class poll_session {

    var $expire;
    var $table;
    var $db;

    function poll_session() {
        global $POLLTBL;
        $this->table = $POLLTBL;
        $this->expire = 7200;
    }

    function set_session_time($expire_time='') {
        if ($expire_time>0) {
            $this->expire = $expire_time;
        }
    }

    function is_valid_session($session,$user_id) {
        $this->db->query("SELECT session, last_visit from ".$this->table['poll_user']." WHERE session='$session' and user_id='$user_id'");
        $row = $this->db->fetch_array($this->db->result);
        if ($row) {            
            return ($this->expire + $row['last_visit'] > time()) ? $row["session"] : false;
        } else {
            return false;
        }
    }

    function is_valid_user($user_id) {
        $this->db->query("SELECT username FROM ".$this->table['poll_user']." WHERE user_id='$user_id'");
        $this->db->fetch_array($this->db->result);
        return ($this->db->record) ? true : false;
    }

    function generate_new_session_id($user_id) {
        srand((double)microtime()*1000000);
        $session = md5 (uniqid (rand()));
        $timestamp = time();
        $this->db->query("UPDATE ".$this->table['poll_user']." SET session='$session', last_visit='$timestamp' WHERE user_id='$user_id'");
        return $session;
    }

    function check_pass($username,$password) {
        if (get_magic_quotes_gpc()) {            
            $password = stripslashes($password);
        } else {
            $username = addslashes($username);
        }
        $password = md5($password);  
        $this->db->query("SELECT user_id FROM ".$this->table['poll_user']." WHERE username='$username' and userpass='$password'");
        $this->db->fetch_array($this->db->result);
        return ($this->db->record) ? $this->db->record["user_id"] : false;
    }

    function check_session_id() {
        global $username, $password, $session, $uid;
        if (isset($session) && isset($uid)) {
            return ($this->is_valid_session($session,$uid)) ? array("session" => "$session", "uid" => "$uid") : false;
        } elseif (isset($username) && isset($password)) {
            $ID = $this->check_pass($username,$password);
            if ($ID) {
                $session = $this->generate_new_session_id($ID);
                return array("session" => "$session", "uid" => "$ID");
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

}

?>