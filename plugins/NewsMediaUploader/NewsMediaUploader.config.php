<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

$config['NMU_MAX_FILESIZE'] = "8mb";
$config['NMU_ACCEPTED_FILES'] = "jpeg,jpg,png,gif";
$config['NMU_UPLOAD_DIR'] = "news_img";
$config['NMU_ALLOW_ANON'] = 0;
$config['NMU_ACL_CHECK'] = 0;
$config['NMU_ACL_LIST'] = "register_users||admin_all";
$config['NMU_CREATE_IMG_THUMBS'] = 1;
$config['NMU_CREATE_IMG_MOBILE'] = 1;
$config['NMU_CREATE_IMG_DESKTOP'] = 1;
$config['NMU_THUMBS_WIDTH'] = 150;
$config['NMU_MOBILE_WIDTH'] = 300;
$config['NMU_DESKTOP_WIDTH'] = 800;
$config['NMU_USER_IMG_LIST_MAX'] = 10;
//$config[''] = ;