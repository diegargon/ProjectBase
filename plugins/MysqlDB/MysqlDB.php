<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

global $dblink;

function MysqlDB_Init() {

    if (DEBUG_PLUGINS_LOAD) { print_debug("MysqlDB Initialice<br/>"); }

    includeConfig("MysqlDB");
    require_once("MysqlDB.inc.php");

    register_action("finalize", "MysqlDB_Close", "5");
    db_connect();
}

function MysqlDB_Close() {
    db_close();    
}