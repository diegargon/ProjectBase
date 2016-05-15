<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */

require_once "includes/core.inc.php";

    

do_action("logout_page");

 

tpl_build_page();

do_action("close_plugin"); 
