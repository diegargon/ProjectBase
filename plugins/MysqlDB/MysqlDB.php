<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function MysqlDB_Init() {
    global $db, $cfg;

    print_debug("MysqlDB Initialice", "PLUGIN_LOAD");
    includePluginFiles("MysqlDB");

    !isset($db) ? $db = new Database : null;
    $db->connect($cfg);
    //register_action("finalize", array($db, "close"), "5"); we use destructor now;
}
