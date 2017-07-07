<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

define('TPL', true);
//define('TPL_DEBUG', true);

$cfg['NAV_MENU'] = 1;
$cfg['HEADER_MENU_HOME'] = 1;
$cfg['IMG_HOME'] = $cfg['STATIC_SRV_URL'] . "plugins/tplBasic/img/home.png";
$cfg['CSS_OPTIMIZE'] = 0; //need cache writable
$cfg['CSS_INLINE'] = 1;
$cfg['STATS_QUERYS'] = 1;
