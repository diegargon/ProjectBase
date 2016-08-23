<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

plugin_start("SMBasic");

$sm->sessionDestroy();
header('Location: ./');
