<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

class Database {

    private $dblink;
    private $query_stats;
    private $db_prefix;
    private $charset;
    private $search_min_char;

    function __construct() {
        $this->query_stats = 0;
    }

    function __destruct() {
        $this->close();
    }

    function connect($cfg) {
        $this->db_prefix = $cfg['DB_PREFIX'];
        $this->charset = DB_CHARSET;
        $this->search_min_char = $cfg['L_SEARCH_MIN_CHAR'];

        $this->dblink = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB);
        if ($this->dblink->connect_errno) {
            printf("Failed to connect to database: %s\n ", $this->dblink->connect_error);
            exit();
        }
        $this->query("SET NAMES " . DB_CHARSET . "");

        return true;
    }

    function query($string) {
        $this->query_stats++;
        $query = $this->dblink->query($string) or $this->dbdie($query);
        return $query;
    }

    function fetch($query) {
        return $row = $query->fetch_assoc();
    }

    function escape($var) {
        return $this->dblink->real_escape_string($var);
    }

    function escape_strip($var) {
        return $this->dblink->real_escape_string(strip_tags($var));
    }

    function num_rows($query) {
        return $query->num_rows;
    }

    function close() {
        !$this->dblink ? die('Could not connect: ' . $this->dblink->error) : false;
        $this->dblink->close();
    }

    private function dbdie($query) {
        printf("\n<b>Error: Unable to retrieve information.</b>");
        printf("\n<br>%s", $query);
        printf("\n<br>reported: %s", $this->dblink->error);
        $this->close();
        exit;
    }

    function insert_id() {
        if (!($id = $this->dblink->insert_id)) {
            die('Could not connect: ' . $this->dblink->error);
            $this->dblink->close();
            exit;
        }

        return $id;
    }

    function free(& $query) {
        $query->free();
    }

    function get_next_num($table, $field) {

        if (empty($table) || empty($field)) {
            return false;
        }
        $table = $this->db_prefix . $table;
        $q = "SELECT MAX( $field ) AS max FROM `$table`;";
        $query = $this->query($q);
        $row = $this->fetch($query);

        return ++$row['max'];
    }

    /*
     * $db->select_all("users", array('uid' => 1, 'username' => "myname"), "LIMIT 1"); 
     * Especify operator default '=';
     * $query = $db->select_all("news", array ("frontpage" => array("value"=> 1, "operator" => "="), "moderation" => 0, "disabled" => 0));
     * extra not array 
     */

    function select_all($table, $where = null, $extra = null, $logic = "AND") {

        if (empty($table)) {
            return false;
        }
        $q = "SELECT * FROM " . $this->db_prefix . $table;

        if (!empty($where)) {
            $q .= " WHERE ";
            $q .= $this->where_process($where, $logic);
        }
        !empty($extra) ? $q .= " $extra" : false;

        return $this->query($q);
    }

    /* */

    function search($table, $s_fields, $searchText, $where = null, $extra = null) {

        $s_words_ary = explode(" ", $searchText);
        $fields_ary = explode(" ", $s_fields);

        $where_s_fields = "";
        $where_s_tmp = "";
        $q = "SELECT * FROM " . $this->db_prefix . $table . " WHERE ";

        if (!empty($where)) {
            $q .= $this->where_process($where, $logic = "AND");
            $q .= " AND ";
        }

        foreach ($fields_ary as $field) {
            !empty($where_s_fields) ? $where_s_fields .= " OR " : false;

            foreach ($s_words_ary as $s_word) {
                if (mb_strlen($s_word, $this->charset) > $this->search_min_char) {
                    !empty($where_s_tmp) ? $where_s_tmp .= " AND " : false;
                    $where_s_tmp .= " $field LIKE '%$s_word%' ";
                }
            }
            !empty($where_s_tmp) ? $where_s_fields .= $where_s_tmp : false;
            $where_s_tmp = "";
        }

        if (!empty($where_s_fields)) {
            $q .= "(" . $where_s_fields . ")";
        } else {
            return false;
        }
        !empty($extra) ? $q .= " $extra " : false;

        return $this->query($q);
    }

    /*  */

    function update($table, $set, $where = null, $extra = null, $logic = "AND") {

        $q = "UPDATE " . $this->db_prefix . $table . " SET ";

        if (empty($set) || empty($table)) {
            return false;
        }
        $q .= $this->set_process($set);

        if (!empty($where)) {
            $q .= " WHERE ";
            $q .= $this->where_process($where, $logic);
        }
        !empty($extra) ? $q .= " $extra" : false;
        return $this->query($q);
    }

    /*  */

    function insert($table, $insert_data, $extra = null) {

        if (empty($table) || empty($insert_data)) {
            return false;
        }
        $insert_ary = $this->insert_process($insert_data);
        $q = "INSERT INTO " . $this->db_prefix . $table . " ( {$insert_ary['fields']} ) VALUES ( {$insert_ary['values']} ) $extra";

        return $this->query($q);
    }

    function delete($table, $where, $extra = null, $logic = 'AND') {

        if (empty($table) || empty($where)) {
            return false;
        }
        $q = "DELETE FROM " . $this->db_prefix . $table . " WHERE ";
        $q .= $this->where_process($where, $logic);
        !empty($extra) ? $q .= " $extra" : false;

        return $this->query($q);
    }

    function upsert($table, $set_ary, $where_ary) {
        $insert_data = array_merge($where_ary, $set_ary);
        $set_data = $this->set_process($set_ary);
        $this->insert($table, $insert_data, "ON DUPLICATE KEY UPDATE $set_data");
    }

    function num_querys() {
        return $this->query_stats;
    }

    private function insert_process($insert_data) {
        foreach ($insert_data as $field => $value) {
            $fields_ary[] = $field;
            $values_ary[] = "'" . $value . "'";
        }
        $insert['fields'] = implode(', ', $fields_ary);
        $insert['values'] = implode(', ', $values_ary);

        return $insert;
    }

    private function set_process($set) {
        foreach ($set as $field => $value) {
            $newset[] = "$field = " . "'" . $value . "'";
        }
        $q = implode(',', $newset);
        return $q;
    }

    private function where_process($where, $logic) {

        foreach ($where as $field => $value) {
            if (!is_array($value)) {
                $q_where_fields[] = "$field = " . "'" . $value . "'";
            } else {
                $q_where_fields[] = "$field {$value['operator']} " . $value['value'];
            }
        }
        $q = implode(" $logic ", $q_where_fields);
        return $q;
    }

}
