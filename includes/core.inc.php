<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

global $debug;

$debug = [];

require_once "config/config.inc.php";
file_exists("tpl/config/config.php") ? require_once "tpl/config/config.inc.php" : false; //rewrite config

require_once "includes/valfilters.inc.php";
require_once "includes/core.functions.php";
require_once "includes/plugins.php";

do_action("init_core");