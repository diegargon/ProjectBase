<?php
/*
	
*/
define('IN_WEB', TRUE);

require_once "includes/core.inc.php";

do_action("news_page");
$tpl->build_page();
do_action("finalize"); 