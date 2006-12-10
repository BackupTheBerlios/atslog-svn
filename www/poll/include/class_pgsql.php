<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

class polldb_sql {
    
    var $conn_id;
    var $result;
    var $record;
    var $db;
    var $row;
    var $end_row;
    var $port;

    function polldb_sql() {
        global $POLLDB;
        $this->db = $POLLDB;
        $this->row = array();
        $this->end_row = array();
        if(ereg(":",$this->db['host'])) {
            list($host,$port) = explode(":",$this->db['host']);
            $this->port = $port;
        } else {
            $this->port = 5432;
        }
    }

    function connect() {
        $pg_connect_str = "";
        if($this->db['user']) {
            $pg_connect_str .= "user=".$this->db['user']." ";
        }
        if($this->db['pass']) {
            $pg_connect_str .= "password=".$this->db['pass']." ";
        }
        if($this->db['host'] != "localhost") {
            $pg_connect_str .= "host=".$this->db['host']." ";
        }
        $pg_connect_str .= "port=$this->port ";
        $pg_connect_str .= "dbname=".$this->db['dbName']." ";
        $this->conn_id = pg_connect($pg_connect_str);
        if (!$this->conn_id) {
            $this->sql_error("Connection Error");
        }
        return $this->conn_id;
    }

    function query($query_strg) {
        $query_strg = eregi_replace("limit ([0-9]+),([ 0-9]+)", "limit \\2, \\1", $query_strg);
        $this->result = pg_exec($this->conn_id,$query_strg);
        if (!$this->result) {
            $this->sql_error("Query Error");
        }
        return $this->result;
    }

    function fetch_array($query_id) {        
        if (!isset($this->row[$query_id])) {
            $this->row[$query_id] = 0;
            $this->end_row[$query_id] = $this->num_rows($query_id);
        }
        if ($this->end_row[$query_id] > $this->row[$query_id]) {
            $this->record = pg_fetch_array($query_id,$this->row[$query_id],PGSQL_ASSOC);
            $this->row[$query_id] ++;
            return $this->record;
        } else {
            unset($this->row[$query_id]);
            $this->record = false;
            return $this->record;
        }
    }

    function num_rows($query_id) {
        return ($query_id) ? pg_NumRows($query_id) : 0;
    }

    function num_fields($query_id) {
        return ($query_id) ? pg_NumFields($query_id) : 0;
    }

    function free_result($query_id) {
        return pg_FreeResult($query_id);
    }

    function affected_rows($query_id='') {
        if (empty($query_id)) {
            $query_id = $this->result;
        }
        return pg_cmdTuples($query_id);
    }

    function close_db() {
        if($this->conn_id) {
            return pg_close($this->conn_id);
        } else {
            return false;
        }
    }
   
    function sql_error($message) {
        $description = pg_errormessage();
        $error ="MySQL Error : $message\n";
        $error.="Message     : $description\n";
        $error.="Date        : ".date("D, F j, Y H:i:s")."\n";
        $error.="IP          : ".getenv("REMOTE_ADDR")."\n";
        $error.="Browser     : ".getenv("HTTP_USER_AGENT")."\n";
        $error.="Referer     : ".getenv("HTTP_REFERER")."\n";
        $error.="PHP Version : ".PHP_VERSION."\n";
        $error.="OS          : ".PHP_OS."\n";
        $error.="Server      : ".getenv("SERVER_SOFTWARE")."\n";
        $error.="Server Name : ".getenv("SERVER_NAME")."\n";
        echo "<b><font size=4 face=Arial>$message</font></b><hr>";
        echo "<pre>$error</pre>";
        exit();
    }

}

?>