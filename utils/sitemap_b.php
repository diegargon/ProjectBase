<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
define('IN_WEB', "TRUE");
define("NL", "\n");


error_reporting(E_ERROR | E_WARNING | E_PARSE);

require("../config.inc.php");

/*
  config.inc.php for:
  $cfg['WEB_URL'] = "http://example.com/";
  $cfg['CHARSET'] = "UTF-8";

 */
require("sitemap.inc.php");

global $url_scanned;
global $url_skip;
global $cfg;
global $counter;

$url_scanned = [];
$url_list = [];
$url_skip_regx = [];
$cfg = [];

$url_skip_regx[] = "/\/profile/i";
$url_skip_regx[] = "/news_/i";

$cfg['CHARSET'] = $cfg['CHARSET'];
$cfg['WEB_URL'] = $cfg['WEB_URL'];
//$cfg['WEB_URL'] = "https://www.meneame.net/";
$cfg["SITEMAP_FILE"] = "sitemap.xml";
$cfg["USER_AGENT"] = "Mozilla/5.0 (compatible; Sitemap Generator)";

$cfg["HOST"] = parse_url($cfg['WEB_URL'], PHP_URL_HOST);
echo "Host : {$cfg['HOST']}\n";

$cfg["DFL_FRQ"] = "daily";
$cfg["DFL_PRIO"] = 0.5;
$cfg["MAX_DEPTH"] = 1;

robots_check();

Scan($cfg['WEB_URL']);
build_sitemap();
//foreach ($url_list as $key => $value) { echo " '$key'-> '$value' \n"; }

