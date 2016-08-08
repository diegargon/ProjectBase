<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsMediaUploader_init() { 
    global $tpl;
    print_debug("NewsMediaUploader initiated", "PLUGIN_LOAD");
    
    includePluginFiles("Template");    
    //$tpl->getCSS_filePath("NewsMediaUploader");
    //$tpl->getCSS_filePath("NewsMediaUploader", "NewsMediaUploader-mobile");
}
