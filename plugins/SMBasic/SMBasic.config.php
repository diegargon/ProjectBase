<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

global $cfg;
define('SM', true); //SESSION MANAGER TRUE
define('SM_DEBUG', true);

$cfg['smbasic_default_session'] = 1; //use php build in or not
$cfg['smbasic_session_start'] = 0; //start session_start, ignored in default session.
$cfg['smbasic_persistence'] = 1;
$cfg['smbasic_max_email'] = 60;
$cfg['sm_max_password'] = 60;
$cfg['sm_min_password'] = 8;
$cfg['smbasic_max_username'] = 32;
$cfg['smbasic_min_username'] = 4;
$cfg['smbasic_use_salt'] = 1;
$cfg['smbasic_pw_salt'] = "5565";
//$cfg['smbasic_cookie_domain'] = "envigo.net";
$cfg['smbasic_session_expire'] = 86400;
$cfg['smbasic_cookie_expire'] = 86400;
$cfg['smbasic_cookie_prefix'] = "projectbase_";
$cfg['smbasic_check_user_agent'] = 0; // activate give problems on movile devices
$cfg['smbasic_check_ip'] = 1;
$cfg['smbasic_need_username'] = 1;
$cfg['smbasic_email_confirmation'] = 1;
// Profile
$cfg['smbasic_can_change_username'] = 1;
$cfg['smbasic_can_change_email'] = 1;
$cfg['SMB_IMG_DFLT_AVATAR'] = "/plugins/SMBasic/tpl/img/avatar.png";
$cfg['smbasic_https_remote_avatar'] = 1;
$cfg['smbasic_session_salt'] = 'y1!^!ob32a.,$!!$3]Q&%@/^^i@?Xx]';
$cfg['smbasic_oauth'] = 0;
$cfg['smbasic_oauth_facebook'] = 0;
$cfg['smbasic_fb_appid'] = '18312566913815977';
$cfg['smbasic_fb_appSecret'] = 'edeb745fd902dbce866a84e6855b1ed06';
