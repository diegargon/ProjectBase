<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function news_page_main() {
    global $tpldata, $config, $LANGDATA, $acl_auth;
    
    //TODO: Split/simplified in functions
    
    if( (empty($_GET['nid'])) || ($nid = S_VAR_INTEGER($_GET['nid'], 8, 1)) == false) {
        $tpldata['E_MSG'] = $LANGDATA['L_NEWS_NOT_EXIST'];
        addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box"));
        return false;
    }
    if ( S_GET_INT("admin") && ( $newslang = S_GET_CHAR_AZ("newslang", 2, 1) ) ) {
       if (defined("ACL") && "ACL") {
           if($acl_auth->acl_ask("admin_all") || $acl_auth->acl_ask("news_admin")) {
               //Do nothing
            } else {           
               $tpldata['E_MSG'] = $LANGDATA['L_ERROR_NOACCESS'];
               addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box")); 
               return false;                
           }
       }
       if ( (($news_row = get_news_byId($nid, $newslang)) == 403) )  {
           $tpldata['E_MSG'] = $LANGDATA['L_ERROR_NOACCESS'];
           addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box")); 
           return false; 
       }       
    } else if ( ($news_row = get_news_byId($nid, $config['WEB_LANG'])) == 403)  {
        $tpldata['E_MSG'] = $LANGDATA['L_ERROR_NOACCESS'];
        addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box")); 
        return false; 
    } else if ($news_row == false) {
        if( ($news_row = get_news_byId($nid)) == false) {
            $tpldata['E_MSG'] = $LANGDATA['L_NEWS_NOT_EXIST'];
            addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box"));
            return false;
        } else {
            $tpldata['E_MSG'] = $LANGDATA['L_NEWS_WARN_NOLANG'];
            addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box"));
            return false;            
        }        
    }  
    if ($config['NEWS_MODERATION'] && $news_row['moderation'] && !S_GET_INT("admin") ) {
            $tpldata['E_MSG'] = $LANGDATA['L_NEWS_ERROR_WAITINGMOD'];
            addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box"));
            return false;        
    }     
    //ADMIN
    if (defined("ACL") && "ACL") {
        if($acl_auth->acl_ask("admin_all") || $acl_auth->acl_ask("news_admin")) {
            addto_tplvar("NEWS_ADMIN_NAV", Newspage_AdminOptions($news_row));
            
            if (!empty($_GET['news_delete']) && !empty($_GET['lang_id']) &&
                    $_GET['news_delete'] > 0 && $_GET['lang_id'] > 0) {
                news_delete(S_GET_INT("news_delete"), S_GET_INT("lang_id"));
                news_redirect();
            }
            if (!empty($_GET['news_approved']) && !empty($_GET['lang_id']) &&
                    $_GET['news_approved'] > 0 && $_GET['lang_id'] > 0) {
                news_approved(S_GET_INT("news_approved"), S_GET_INT("lang_id"));
                news_redirect();
            }
            if (isset($_GET['news_featured']) && !empty($_GET['lang_id'] && !empty($_GET['nid']))) {
                empty($_GET['news_featured']) ? $news_featured = 0: $news_featured =1;                
                news_featured(S_GET_INT("nid", 11, 1), $news_featured, S_GET_INT("lang_id"));
                news_redirect();
            }

            if (isset($_GET['news_frontpage'])  && !empty($_GET['lang_id'])) {
               news_frontpage(S_GET_INT("nid", 11, 1), S_GET_INT("lang_id"), S_GET_INT("news_frontpage", 1, 1));
               news_redirect();    
            }             
        }
    }
    $tpldata['NID'] = $news_row['nid'];    
    $tpldata['NEWS_TITLE'] = $news_row['title'];    
    $tpldata['NEWS_LEAD'] = $news_row['lead'];    
    $tpldata['NEWS_URL'] = "news.php?nid=$news_row[nid]";
    $tpldata['NEWS_DATE'] = format_date($news_row['date']);
    $tpldata['NEWS_AUTHOR'] = $news_row['author'];
    $tpldata['NEWS_TEXT']  = $news_row['text'];

    if ( ($allmedia = get_news_media_byID($nid)) != false) {
        foreach ($allmedia as $media) {
            if($media['itsmain'] == 1 ) {
                $tpldata['NEWS_MAIN_MEDIA'] = news_format_media($media);
            }
        }
    }    
    addto_tplvar("POST_ACTION_ADD_TO_BODY", getTPL_file("Newspage", "news_show_body"));                                               
}

function Newspage_AdminOptions($news) {
    global $LANGDATA, $config;
    $content = "<div id='adm_nav_container'>";
    $content .= "<nav id='adm_nav'>";
    $content .= "<ul>";
    $content .= "<li><a href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&newsedit={$news['nid']}&lang_id={$news['lang_id']}'>{$LANGDATA['L_NEWS_EDIT']}</a></li>";
    if ($news['featured'] == 1) {
        $content .= "<li><a class='link_active' href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_featured=0&featured_value=0&lang_id={$news['lang_id']}&admin=1''>{$LANGDATA['L_NEWS_FEATURED']}</a></li>";    
    } else {
        $content .= "<li><a href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_featured=1&featured_value=1&lang_id={$news['lang_id']}&admin=1''>{$LANGDATA['L_NEWS_FEATURED']}</a></li>";    
    }
    if ($news['moderation']) {
        $content .= "<li><a href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_approved={$news['nid']}&lang_id={$news['lang_id']}&admin=1'>{$LANGDATA['L_NEWS_APPROVED']}</a></li>";
    }
    //$content .= "<li><a href=''>{$LANGDATA['L_NEWS_DISABLE']}</a></li>";
    $content .= "<li><a href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_delete={$news['nid']}&lang_id={$news['lang_id']}&admin=1&return_home=1' onclick=\"return confirm('{$LANGDATA['L_NEWS_CONFIRM_DEL']}')\">{$LANGDATA['L_NEWS_DELETE']}</a></li>";
    if ($config['NEWS_SELECTED_FRONTPAGE'] ){
        if ($news['frontpage'] == 1) {
            $content .= "<li><a class='link_active' href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_frontpage=0&lang_id={$news['lang_id']}'>{$LANGDATA['L_NEWS_FRONTPAGE']}</a></li>";        
        } else {
            $content .= "<li><a href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_frontpage=1&lang_id={$news['lang_id']}'>{$LANGDATA['L_NEWS_FRONTPAGE']}</a></li>";
        }
    }
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

function news_featured($nid, $featured, $lang_id) {
    global $config;
    
    if (!empty($nid) && !empty($lang_id)) {            
        $q = "UPDATE {$config['DB_PREFIX']}news  SET featured = '$featured' WHERE nid = '$nid' AND lang_id = '$lang_id' ";
        db_query($q);
        if($featured == 1) {
            $q2 = "UPDATE {$config['DB_PREFIX']}news  SET featured = '0' WHERE  lang_id = '$lang_id' AND nid != '$nid'";
            db_query($q2);
        }
    } else {
        return false;
    }
    return true;    
}

function news_frontpage($nid, $lang_id, $frontpage_state) {
    global $config;

    if (empty($frontpage_state)) {
        $frontpage_state = 0;
    }
    if (!empty($nid) && isset($frontpage_state) && !empty($lang_id) && $nid > 0 && $lang_id > 0) {            
        $q = "UPDATE {$config['DB_PREFIX']}news  SET frontpage = '$frontpage_state' WHERE nid = '$nid' AND lang_id = '$lang_id' ";        
        db_query($q);
    } else {
        return false;
    }
    
    return true;    
}

function news_redirect()  {
    if(!empty($_GET['return_home'])) {
        header("Location: / ");                
    } else {        
        header("Location: {$_SERVER['HTTP_REFERER']} ");  //TODO FILTER
    }    
}
