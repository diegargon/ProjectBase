<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function Template_init() {
    global $tpl;
    print_debug("Template initiated", "PLUGIN_LOAD");

    includePluginFiles("Template");
    //$tpl->getCSS_filePath("Template");
    //$tpl->getCSS_filePath("Template", "Template-mobile");
}
