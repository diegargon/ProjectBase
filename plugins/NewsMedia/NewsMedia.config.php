<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

$config['ACCEPTED_MEDIA_REGEX'] = "jpe?g|bmp|png|JPE?G|BMP|PNG";
$config['NEWS_MEDIA_MAX_LENGHT'] = 200;
$config['NEWS_MEDIA_MIN_LENGHT'] = 10;
$config['NEWS_ADD_MAIN_MEDIA'] = 1;
//$config['NEWS_MAIN_MEDIA_REQUIRED'] = 1;
$config['NEWS_ADD_EXTRA_MEDIA'] = 1;