<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

class Database {
    var $dblink;
    
    function connect() {
        $this->dblink = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB);
        if (!$this->dblink) {
            die ('Failed to connect to MySQL: ' . $this->mysqli->connect_error());
            exit();
        }   
        $this->query("SET NAMES ". DB_CHARSET ."");
    
    return true;
    }

    function query($string) {        
        $query = $this->dblink->query($string) or $this->dbdie($query);
        return $query;
    }

    function fetch($query) {
    	$row = $query->fetch_assoc();
	return $row;
    }

    function escape_string($var) { 
        return $this->dblink->real_escape_string($var);        
    }

    function num_rows($query) {
	return $query->num_rows;
    }

    function close() {    
	if (!$this->dblink) {
	    die('Could not connect: ' . $this->dblink->error);
	}
	$this->dblink->close();
    }

    private function dbdie($query) {
        echo "\n<b>Error: Unable to retrieve information.</b>";
        echo "\n<br>$query";
        echo "\n<br>reported: ".$this->dblink->error;
        $this->close();
       exit;
    }

    function insert_id() {
        if(!($id = $this->dblink->insert_id()) ) {
            die('Could not connect: ' . $this->dblink->error);
            $this->dblink->close();
            exit;
        }

        return $id;
    }

    function free_result(& $query) {
        $query->free();
        
    }

    function get_next_num ($field, $table) {
        $q = "SELECT MAX( $field ) AS max FROM `$table`;";    
        $query = $this->query($q);
        $row = $this->fetch($query);
    
        return ++$row['max'];
    }    
    
    function select($table, $where = null) {
        global $config;
        $q = "SELECT * FROM {$config['DB_PREFIX']}$table";
        if (!empty($where)) {
            $q .= " WHERE ";
            foreach ($where as $field => $value){
                 $q_options[] = "$field = " . "'". $value ."'";
            }
            $q .= implode( ' AND ', $q_options );
        }
        return $this->query($q);
    }
    
}
