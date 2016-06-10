<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

global $dblink;

function MysqlDB_Init() {
    global $db;
    
    print_debug("MysqlDB Initialice", "PLUGIN_LOAD");

    includePluginFiles("MysqlDB");    

    //old way remove later
    //register_action("finalize", "MysqlDB_Close", "5");
    //db_connect();

    //both methods until migrate all
    if (!isset($db)) {
        $db = new Database;
    }
    register_action("finalize", array($db, "close"), "5");
    $db->connect();

}

/* remove later
function MysqlDB_Close() {
    global $db;
    $db->close();
    db_close();    
}
 * 
 */