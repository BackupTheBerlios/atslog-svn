<?php
/**
 * ----------------------------------------------
 * Advanced Poll 2.0.3 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

class input2db extends polldb_sql {

    function input2db() {
        $this->polldb_sql();
        $this->connect();
    }

    function format_array($vars_array='') {
        if (is_array($vars_array)) {
            reset($vars_array);
            if (get_magic_quotes_gpc()) {
                while (list($var, $value)=each($vars_array)) {
                    $vars_array[$var] = trim($value);
                }
            } else {
                while (list($var, $value)=each($vars_array)) {
                    $vars_array[$var] = trim(addslashes($value));
                }    
            }
            reset($vars_array);
            return $vars_array;
        } else {
            return false;
        }
    }
    
    function insert_db_row($table,$vars='') {
        if (!is_array($vars)) {
            return false;
        }
        $vars = $this->format_array($vars);
        while (list($var,$value)=each($this->VARS)) {
            $arr_var[] = $var;
            $arr_value[] = "'$value'";
        }
        $tb_var = implode(",",$arr_var);
        $tb_value = implode(",",$arr_value);
        $this->query("INSERT INTO $table ($tb_var) VALUES ($tb_value)");
        return ($this->result) ? true : false;
    }
    
    function update_db_row($table,$vars='',$id,$row_id) {
        if (!is_array($vars)) {
            return false;
        }
        $vars = $this->format_array($vars);
        if (sizeof($vars)) {
            $query ='';
            while (list($var,$value)=each($vars)) {
                $query .= "$var='$value',";
            }
            $query = substr($query, 0, strlen($query)-1);
            $this->query("UPDATE $table SET $query WHERE($id=$row_id)");
            return true;
        } else {
            return false;
        }
    }


}

?>