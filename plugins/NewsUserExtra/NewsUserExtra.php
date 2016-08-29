<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsUserExtra_init() {
    global $tpl;
    print_debug("NewsUserExtra initiated", "PLUGIN_LOAD");

    includePluginFiles("NewsUserExtra");
    //$tpl->getCSS_filePath("NewsUserExtra");
    //$tpl->getCSS_filePath("NewsUserExtra", "NewsUserExtra-mobile");
}
