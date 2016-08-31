<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

global $config;
define('SM', true); //SESSION MANAGER TRUE
define('SM_DEBUG', true);

$config['smbasic_session_persistence'] = 1;
$config['smbasic_max_email'] = 60;
$config['sm_max_password'] = 60;
$config['sm_min_password'] = 8;
$config['smbasic_max_username'] = 32;
$config['smbasic_min_username'] = 4;
$config['smbasic_use_salt'] = 1;
$config['smbasic_salt'] = "5565";
$config['smbasic_public_salt'] = "4455";
//$config['smbasic_cookie_domain'] = "envigo.net";
$config['smbasic_session_expire'] = 86400;
$config['smbasic_cookie_expire'] = 86400;
$config['smbasic_cookie_prefixname'] = "projectbase_";
$config['smbasic_check_user_agent'] = 0; // activate give problems on movile devices
$config['smbasic_check_ip'] = 1;
$config['smbasic_need_username'] = 1;
$config['smbasic_email_confirmation'] = 1;
// Profile
$config['smbasic_can_change_username'] = 1;
$config['smbasic_can_change_email'] = 1;
$config['SMB_IMG_DFLT_AVATAR'] = "/plugins/SMBasic/tpl/img/avatar.png";
