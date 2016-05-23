<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */

global $debug;
global $external_scripts;
$debug = [];
$external_scripts =[];

require_once "config/config.inc.php";
require_once "includes/valfilters.inc.php";
require_once "includes/core.functions.php";
require_once "includes/plugins.php";

do_action("core_action");