<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
define('RECAPTCHA', true);

$cfg['RC_PUBLIC_KEY'] = "";
$cfg['RC_PRIVATE_KEY'] = "";
$cfg['RC_VERIFY_URL'] = "https://www.google.com/recaptcha/api/siteverify";
//$cfg[''] = ;