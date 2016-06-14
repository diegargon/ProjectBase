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
            die ('Failed to connect to database: ' . $this->mysqli->connect_error());
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

    function escape($var) { 
        return $this->dblink->real_escape_string($var);        
    }
    function escape_strip($var) { 
        $var = strip_tags($var);
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

    function free(& $query) {
        $query->free();
        
    }

    function get_next_num ($table, $field) {
        global $config;
       
        if (empty($table) || empty($field)) { return false; }
        
        $table = $config['DB_PREFIX'] . $table;
        $q = "SELECT MAX( $field ) AS max FROM `$table`;";    
        $query = $this->query($q);
        $row = $this->fetch($query);
    
        return ++$row['max'];
    }    
    
    /* $db->select_all("users", array('uid' => 1, 'username' => "myname"), "LIMIT 1"); */
    /* Especify operator default '=';
    /* $query = $db->select_all("news", array ("frontpage" => array("value"=> 1, "operator" => "="), "moderation" => 0, "disabled" => 0));
    /* extra not array */
    function select_all($table, $where = null, $extra = null, $logic = "AND") { 
        global $config;

        if(empty($table)) {
            return false;
        }        
        
        $q = "SELECT * FROM {$config['DB_PREFIX']}$table";
        
        if (!empty($where)) {
            $q .= " WHERE ";
            foreach ($where as $field => $value){
                if (!is_array($value)) {
                 $q_where_fields[] = "$field = " . "'". $value ."'";
                } else {
                    $q_where_fields[] = "$field {$value['operator']} " . "'". $value['value'] ."'";
                }
            }
            $q .= implode( " $logic ", $q_where_fields );
        }
        
        if( !empty($extra) ) {
            $q .= " $extra";
        }
        return $this->query($q);
    }
    
    /*
     *
     */
    function update($table, $set, $where = null) {
        global $config;

        $q = "UPDATE {$config['DB_PREFIX']}$table SET ";

        if(empty($set) || empty($table)) {
            return false;
        }
        
        foreach ($set as $field => $value) {
             $q_set_fields[] = "$field = " . "'". $value ."'";
        } 
        $q .= implode( ',', $q_set_fields );
        
        if (!empty($where)) {
            $q .= " WHERE ";
            foreach ($where as $field => $value){
                if (!is_array($value)) {
                 $q_where_fields[] = "$field = " . "'". $value ."'";
                } else {
                    $q_where_fields[] = "$field {$value['operator']} " . "'". $value['value'] ."'";
                }
            }
            $q .= implode( ' AND ', $q_where_fields );
        } 
        
        return $this->query($q);
    }
    
    /*  */
    function insert($table, $data) {
        global $config;

        if (empty($table) || empty($data)) { return false; }
        
        foreach ($data as $field => $value){        
            $fields_ary[] = $field;
            $values_ary[] = "'". $value . "'";
        }
        $fields = implode( ', ', $fields_ary );
        $values = implode( ', ', $values_ary );
        $q = "INSERT INTO {$config['DB_PREFIX']}$table ( $fields ) VALUES ( $values )";        
        
        
        return $this->query($q);
    }
    
    function delete($table, $where, $extra) {
        global $config;
               
        if(empty($table) || empty($where) ) { return false; }
        
        $q = "DELETE FROM {$config['DB_PREFIX']}$table WHERE ";

        foreach ($where as $field => $value) {
             $q_where_fields[] = "$field = " . "'". $value ."'";
        } 
        $q .= implode( ' AND ', $q_where_fields );        
        $q .= " $extra";
        return $this->query($q);
    }
}
