<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }


global $config;
$config = [];
define("DEBUG", true);
define("DEBUG_PLUGINS_LOAD", false);    
    
$config['CHARSET'] = "UTF-8";
$config['WEB_DESC'] = "Noticias";
$config['TITLE'] = "Project Base";
$config['THEME'] = "default";
$config['WEB_URL'] = "http://projectbase.envigo.net/";
$config['WEB_LANG'] = "es";
$config['WEB_DIR'] = "ltr";
$config['WEB_KEYWORDS'] = "test";
$config['WEB_VIEWPORT'] = "width=device-width, initial-scale=1.0";
$config['FRIENDLY_URL'] = 1;
$config['DEFAULT_TIMEZONE'] = "UTC";
$config['DEFAULT_DATEFORMAT'] = "d/m/y H:i";
$config['BACKLINK'] = "./";
//$config['']