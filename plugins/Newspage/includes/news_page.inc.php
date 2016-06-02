<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Newspage_AdminOptions($news) {
    global $LANGDATA;
    $content = "<div id='adm_nav_container'>";
    $content .= "<nav id='adm_nav'>";
    $content .= "<ul>";
    $content .= "<li><a href=''>{$LANGDATA['L_NEWS_EDIT']}</a></li>";
    if ($news['moderation']) {
        $content .= "<li><a href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_approved={$news['nid']}&lang_id={$news['lang_id']}&admin=1'>{$LANGDATA['L_NEWS_APPROVED']}</a></li>";
    }
    //$content .= "<li><a href=''>{$LANGDATA['L_NEWS_DISABLE']}</a></li>";
    $content .= "<li><a href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_delete={$news['nid']}&lang_id={$news['lang_id']}&return_home=1' onclick=\"return confirm('{$LANGDATA['L_NEWS_CONFIRM_DEL']}')\">{$LANGDATA['L_NEWS_DELETE']}</a></li>";
    $content .= "</ul>";
    $content .= "</nav>";
    $content .= "</div>";

    return $content;
}

function news_delete($nid, $lang_id) {
    global $config;
    
    if (!empty($nid) && !empty($lang_id) && $nid > 0 && $lang_id > 0) {    
        $q = "DELETE FROM {$config['DB_PREFIX']}news WHERE nid = '$nid' AND lang_id = '$lang_id' ";
        $q2 = "DELETE FROM {$config['DB_PREFIX']}links WHERE plugin='Newspage' AND source_id = '$nid' ";
        db_query($q) && db_query($q2);
    } else {
        return false;
    }
    return true;
}

function news_approved($nid, $lang_id) {
    global $config;
    
    if (!empty($nid) && !empty($lang_id) && $nid > 0 && $lang_id > 0) {    
        $q = "UPDATE {$config['DB_PREFIX']}news  SET moderation = '0' WHERE nid = '$nid' AND lang_id = '$lang_id' ";
        db_query($q);
    } else {
        return false;
    }
    return true;    
}