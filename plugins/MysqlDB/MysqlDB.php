<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

global $dblink;

function MysqlDB_Init() {
    global $db;
    
    print_debug("MysqlDB Initialice", "PLUGIN_LOAD");
    includePluginFiles("MysqlDB");    

    !isset($db) ? $db = new Database : false;

    register_action("finalize", array($db, "close"), "5");
    $db->connect();
}
