<?php
/*
	File: index.php
*/
define('IN_WEB', TRUE);

require_once "includes/core.inc.php";

do_action("index_page");
$tpl->build_page();
do_action("finalize"); 




