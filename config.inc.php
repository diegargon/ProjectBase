<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

global $cfg;
$cfg = [];
//define("DEBUG", true);
//define("PLUGIN_LOAD", true);    

$cfg['CORE_VERSION'] = "";
$cfg['CHARSET'] = "UTF-8";
$cfg['PAGE_DESC'] = "Noticias";
$cfg['WEB_NAME'] = "Project Base";
$cfg['TITLE'] = $cfg['WEB_NAME'];
$cfg['PAGE_TITLE'] = $cfg['TITLE'];
$cfg['THEME'] = "default";
$cfg['WEB_URL'] = "https://projectbase.envigo.net/";
$cfg['STATIC_SRV_URL'] = $cfg['WEB_URL'];
$cfg['WEB_LOGO'] = $cfg['STATIC_SRV_URL'] . "favicon-96x96.png";
$cfg['WEB_LANG'] = "es";
$cfg['WEB_LANG_NAME'] = "Español";
$cfg['WEB_LANG_ID'] = 1; //used when not ML
$cfg['WEB_DIR'] = "ltr";
$cfg['WEB_DESC'] = "Noticias y algo más";
$cfg['PAGE_KEYWORDS'] = "test";
$cfg['PAGE_VIEWPORT'] = "width=device-width,minimum-scale=1,initial-scale=1";
$cfg['PAGE_AUTHOR'] = "ProjectBase";
$cfg['FRIENDLY_URL'] = 1;
$cfg['DEFAULT_TIMEZONE'] = "UTC";
$cfg['DEFAULT_DATEFORMAT'] = "d/m/y H:i";
$cfg['DB_DATEFORMAT'] = "Y-m-d H:i:s";
$cfg['BACKLINK'] = "javascript:history.go(-1)";
$cfg['REMOTE_CHECKS'] = 1;
$cfg['ACCEPTED_MEDIA_REGEX'] = "jpe?g|bmp|png|JPE?G|BMP|PNG|gif";
$cfg['ADMIN_EMAIL'] = "diego@envigo.net";
$cfg['CONTACT_EMAIL'] = $cfg['ADMIN_EMAIL'];
$cfg['EMAIL_SENDMAIL'] = "no-reply@envigo.net";
$cfg['SERVER_STRESS'] = 0.8;
$cfg['CON_FILE'] = "index.php"; // need change manually in htaccess
$cfg['IMG_SELECTOR'] = "desktop";
$cfg['FOOT_COPYRIGHT'] = "Copyright &copy; 2016 - 2016 Diego García All Rights Reserved";
$cfg['TERMS_URL'] = "Terms";
$cfg['IMG_UPLOAD_DIR'] = "news_img";
$cfg['WELCOME_BOTS'] = "Google|MSN|Yahoo|Lycos|Bing|twitter|Facebook";
$cfg['BAD_BOTS'] = "ia_archiver|Altavista|eStyle|MJ12bot|ips-agent|Yandex|Semrush|Baidu|Sogou|Pcore";
$cfg['INCLUDE_MICRODATA'] = 1;
$cfg['INCLUDE_DATA_STRUCTURE'] = 1;
$cfg['SOCIAL_FACEBOOK_URL'] = "";
$cfg['SOCIAL_TWEETER_URL'] = "";
//$cfg['']