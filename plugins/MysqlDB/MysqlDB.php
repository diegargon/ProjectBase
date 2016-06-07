<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

global $dblink;

function MysqlDB_Init() {
    global $db;
    if (DEBUG_PLUGINS_LOAD) { print_debug("MysqlDB Initialice<br/>"); }

    includePluginFiles("MysqlDB");    
  
    register_action("finalize", "MysqlDB_Close", "5");
    db_connect();
/*
    if (!isset($db)) {
        $db = new Database;
    }
    $db->connect();
    
    $query = $db->select("users", array('uid' => 1, 'username' => "diego"));
    while ($row = $db->fetch($query)) {
      echo "{$row['username']} <br>";  
    }
    //$db->free_result($query);
    $db->close();
*/
}

function MysqlDB_Close() {
    db_close();    
}