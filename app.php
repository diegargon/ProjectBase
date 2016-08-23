<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
define('IN_WEB', TRUE);

require_once "includes/core.inc.php";

$module = S_GET_STRICT_CHARS("module");
$page = S_GET_STRICT_CHARS("page");

if ( empty($module) || empty($page)) {
    echo "Error module or page missed";
    exit();
}
if(!plugin_check_enable($module)) {
    echo "Error plugin ins't enabled";
    exit();
}
$path = "plugins/$module/$page.php";
if (!file_exists($path)) {
    echo "Error page not exist";
    exit();
} else {
    do_action("preload_" . $module ."_". $page);
    require_once($path);
}

$tpl->build_page();
do_action("finalize"); 