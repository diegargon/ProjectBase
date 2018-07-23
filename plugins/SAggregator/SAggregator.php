<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SAggregator_init() {
    global $tpl, $LNG, $cfg, $db, $SAggre, $tUtil, $ctgs, $sm;
    
    print_debug("SAggregator initiated", "PLUGIN_LOAD");

    includePluginFiles("SAggregator");
    $tpl->getCSS_filePath("SAggregator");
    //$tpl->getCSS_filePath("Template", "Template-mobile");
    
!isset($tUtil) ? $tUtil = new TimeUtils($cfg, $db) : null;
    
    $SAggre = new SAggre($cfg, $LNG, $tpl, $db, $tUtil, $ctgs, $sm);
}
