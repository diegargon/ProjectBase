<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

global $config;
$config = [];
define("DEBUG", true);
//define("PLUGIN_LOAD", true);    

$config['CHARSET'] = "UTF-8";
$config['PAGE_DESC'] = "Noticias";
$config['WEB_NAME'] = "Project Base";
$config['TITLE'] = $config['WEB_NAME'];
$config['PAGE_TITLE'] = $config['TITLE'];
$config['THEME'] = "default";
$config['WEB_URL'] = "http://projectbase.envigo.net/";
$config['WEB_LANG'] = "es";
$config['WEB_LANG_NAME'] = "Español";
$config['WEB_LANG_ID'] = "1"; //used when not ML
$config['WEB_DIR'] = "ltr";
$config['WEB_DESC'] = "Noticias y algo más";
$config['PAGE_KEYWORDS'] = "test";
$config['PAGE_VIEWPORT'] = "width=device-width, initial-scale=1.0";
$config['PAGE_AUTHOR'] = "ProjectBase";
$config['FRIENDLY_URL'] = 1;
$config['DEFAULT_TIMEZONE'] = "UTC";
$config['DEFAULT_DATEFORMAT'] = "d/m/y H:i";
$config['BACKLINK'] = "javascript:history.go(-1)";
$config['REMOTE_CHECKS'] = 1;
$config['ACCEPTED_MEDIA_REGEX'] = "jpe?g|bmp|png|JPE?G|BMP|PNG|gif";
$config['ADMIN_MAIL'] = "diego@envigo.net";
$config['EMAIL_SENDMAIL'] = "no-reply@envigo.net";
$config['SERVER_STRESS'] = 0.8;
//$config['']
