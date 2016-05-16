<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */
global $dblink;



function MysqlDB_Init() {
    //global $config;
    
    
    print_debug("MysqlDB Initialice<br/>");
    
    
    require_once("MysqlDB.config.php");
    require_once("MysqlDB.inc.php");

    register_action("close_plugin", "MysqlDB_Close", "5");
   
   
    
    db_connect();
}

function MysqlDB_Close() {
    db_close();
    
}