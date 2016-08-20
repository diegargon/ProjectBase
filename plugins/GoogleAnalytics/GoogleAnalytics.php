<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function GoogleAnalytics_init() { 
    global $tpl;
    print_debug("GoogleAnalytics initiated", "PLUGIN_LOAD");
    
    includePluginFiles("GoogleAnalytics");    
    $tpl->addto_tplvar("SCRIPTS_BOTTOM", $tpl->getTPL_file("GoogleAnalytics"));
}