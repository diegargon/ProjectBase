<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
define('RECAPTCHA', true);

$config['RC_PUBLIC_KEY'] = "";
$config['RC_PRIVATE_KEY'] = "";
$config['RC_VERIFY_URL'] = "https://www.google.com/recaptcha/api/siteverify";
//$config[''] = ;