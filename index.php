<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
define('IN_WEB', TRUE);

require_once "includes/core.inc.php";

$module = S_GET_STRICT_CHARS("module");
$page = S_GET_STRICT_CHARS("page");

if (empty($module) || empty($page)) {
    //exit("Error module or page missed");
    do_action("index_page");
} else {
    !plugin_check_enable($module) ? exit("Error plugin ins't enabled") : null;

    $path = "plugins/$module/$page.php";
    if (!file_exists($path)) {
        exit("Error page not exist");
    } else {
        do_action("preload_" . $module . "_" . $page);
        require_once($path);
    }
}

$tpl->build_page();
do_action("finalize");
