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
$path = "plugins/$module/$page.php";
if (!file_exists($path)) {
    echo "Error page not exist";
    exit();
} else {
    require_once($path);
}


//do_action("root_page");
$tpl->build_page();
do_action("finalize"); 