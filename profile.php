<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
define('IN_WEB', TRUE);

require_once "includes/core.inc.php";

do_action("profile_page");
$tpl->build_page();
do_action("finalize"); 
