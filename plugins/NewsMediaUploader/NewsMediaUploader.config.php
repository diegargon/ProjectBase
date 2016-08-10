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

//$config[''] = ;