<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

global $debug;

$debug = [];

require_once "config.inc.php";
file_exists("config/config.inc.php") ? require_once "config/config.inc.php" : null; //rewrite config

require_once "includes/valfilters.inc.php";
require_once "includes/core.functions.php";
require_once "includes/plugins.php";

mobileDetect() ? $cfg['IMG_SELECTOR'] = "mobile" : null;
$cfg['ITS_BOT'] = botDetect();
do_action("init_core");
