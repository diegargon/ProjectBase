<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SimpleCategories_init() {
    global $ctgs;
    print_debug("SimpleCategories initiated", "PLUGIN_LOAD");

    includePluginFiles("SimpleCategories");
    //$tpl->getCSS_filePath("SimpleCategories");
    //$tpl->getCSS_filePath("SimpleCategories", "SimpleCategories-mobile");
    $ctgs = new Categories();
}
