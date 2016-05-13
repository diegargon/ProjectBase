<?php
/*
	File: index.php
*/

//include files for adding plugin functionality
require_once "includes/core.inc.php";

    

do_action("index_page");

 

tpl_build_page();

do_action("close_plugin"); 




