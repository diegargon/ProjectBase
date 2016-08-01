<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Template_init() { 
    global $tpl;
    print_debug("Template initiated", "PLUGIN_LOAD");
    
    includePluginFiles("Template");    
    //$tpl->getCSS_filePath("Template");
    //$tpl->getCSS_filePath("Template", "Template-mobile");
}
