<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

plugin_start("SMBasic");

$sm->destroy();

header('Location: ./');
