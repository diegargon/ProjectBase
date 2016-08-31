<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

require_once "includes/plugins.inc.php";
require_once "includes/plugins.check.php";

$actions = $registered_plugins = $started_plugins = [];

get_all_enabled_plugins();
start_registered_plugins ();

