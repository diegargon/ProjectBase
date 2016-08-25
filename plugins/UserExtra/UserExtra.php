<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function UserExtra_init() {    
    print_debug("UserExtra initiated", "PLUGIN_LOAD");

    includePluginFiles("UserExtra");    
}
