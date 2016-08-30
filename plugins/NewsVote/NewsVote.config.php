<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

define("NEWS_VOTE", true);

$config['NEWSVOTE_ON_NEWS'] = 1;
$config['NEWSVOTE_ON_NEWS_COMMENTS'] = 1;
$config['NEWSVOTE_STARS_URL'] = "/plugins/NewsVote/tpl/images/";
$config['NEWSVOTE_NEWS_USER_RATING_N'] = 1; //News author
$config['NEWSVOTE_NEWS_USER_RATING_NT'] = 1; //News translator;
$config['NEWSVOTE_COMMENT_USER_RATING'] = 1;
$config['NEWSVOTE_COMMENT_USER_RATING_MODE'] = "div2"; //1 mean one point, div2 divide/2 (round and 1 point at least), 0 = real user rating
//$config[''] = ;