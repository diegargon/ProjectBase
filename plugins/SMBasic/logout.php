<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

plugin_start("SMBasic");

$user = $sm->getSessionUser();

if ($user && $user['uid'] != 0) {
    $sm->destroy();
}
header('Location: ./');