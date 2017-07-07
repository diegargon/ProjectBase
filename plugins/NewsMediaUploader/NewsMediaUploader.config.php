<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

$cfg['NMU_MAX_FILESIZE'] = "8mb";
$cfg['NMU_ACCEPTED_FILES'] = "jpeg,jpg,png,gif";
$cfg['NMU_UPLOAD_DIR'] = $cfg['IMG_UPLOAD_DIR'];
$cfg['NMU_ALLOW_ANON'] = 0;
$cfg['NMU_ACL_CHECK'] = 0;
$cfg['NMU_ACL_LIST'] = "register_users||admin_all";
$cfg['NMU_CREATE_IMG_THUMBS'] = 1;
$cfg['NMU_CREATE_IMG_MOBILE'] = 1;
$cfg['NMU_CREATE_IMG_DESKTOP'] = 1;
$cfg['NMU_THUMBS_WIDTH'] = 250;
$cfg['NMU_MOBILE_WIDTH'] = 300;
$cfg['NMU_DESKTOP_WIDTH'] = 800;
$cfg['NMU_USER_IMG_LIST_MAX'] = 10;
$cfg['NMU_REMOTE_FILE_UPLOAD'] = 1;
//$cfg[''] = ;