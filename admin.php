<?php
/*
	File: index.php
*/
define('IN_WEB', TRUE);

require_once "includes/core.inc.php";

    

do_action("admin_page");

 

tpl_build_page();

do_action("close_plugin"); 




