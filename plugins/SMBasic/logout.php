<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

plugin_start("SMBasic");

$user = $sm->getSessionUser();

if ($user && $user['uid'] != 0) {
    $sm->destroy();
}
header('Location: ./');