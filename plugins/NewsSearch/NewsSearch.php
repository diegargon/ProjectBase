<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsSearch_init() { 
    global $tpl;
    print_debug("NewsSearch initiated", "PLUGIN_LOAD");
    
    includePluginFiles("NewsSearch");    
    //$tpl->getCSS_filePath("NewsSearch");
    //$tpl->getCSS_filePath("NewsSearch", "NewsSearch-mobile");
}
