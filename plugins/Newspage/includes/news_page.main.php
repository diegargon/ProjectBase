<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function news_show_page() {
    global $config, $LANGDATA, $tpl, $sm, $acl_auth;

    if( (empty($_GET['nid'])) || ($nid = S_GET_INT("nid", 8, 1)) == false ||
        (empty($_GET['lang'])) || ($lang = S_GET_CHAR_AZ("lang", 2, 2)) == false) {
        $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box","L_NEWS_NOT_EXIST"));
        return false;
    }     
        
    if ( S_GET_INT("admin") ) {
        if (defined("ACL")) {
            if( !$acl_auth->acl_ask("admin_all||news_admin")) {
                $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box", "L_ERROR_NOACCESS")); 
                return false;                
            }
        } else {
            if ( ($user = $sm->getSessionUser()) == false || $user['isAdmin'] != 1) {
                return false;
            }
        }                
    }       
    news_process_admin_actions(); 
    
    if( ($news_row = news_get_page($nid, $lang)) == false) {
        return false;
    }
     
    $tpl->addto_tplvar("NEWS_ADMIN_NAV", news_nav_options($news_row));
    
    $tpl_data['NID'] = $news_row['nid'];    
    $tpl_data['NEWS_TITLE'] = str_replace('\r\n', '', $news_row['title']);    
    $tpl_data['NEWS_LEAD'] = str_replace('\r\n', PHP_EOL, $news_row['lead']);    
    $tpl_data['NEWS_URL'] = "news.php?nid=$news_row[nid]";
    $tpl_data['NEWS_DATE'] = format_date($news_row['date']);
    $tpl_data['NEWS_AUTHOR'] = $news_row['author'];
    $tpl_data['NEWS_AUTHOR_UID'] = $news_row['author_id'];       
    $tpl_data['NEWS_TEXT']  = str_replace('\r\n', PHP_EOL, $news_row['text']);
    if (!empty ($news_row['translator'])) {
        $translator = $sm->getUserByUsername($news_row['translator']);        
        $tpl_data['NEWS_TRANSLATOR'] = "<a href='/profile.php?lang={$config['WEB_LANG']}&viewprofile={$translator['uid']}'>{$translator['username']}</a>";
    }
    $tpl->addtpl_array($tpl_data);

    if ( ($media = get_news_main_link_byID($news_row['nid'])) != false) {
        $tpl->addto_tplvar("NEWS_MAIN_MEDIA", news_format_media($media));
    }    
    if ( ($news_source = get_news_source_byID($news_row['nid'])) != false  && $config['NEWS_SOURCE'] ) {
        $tpl->addto_tplvar("NEWS_SOURCE", news_format_media($news_source));
    }
    if ( $config['NEWS_RELATED'] && ($news_related = news_get_related($news_row['nid'])) != false) {
        $related_content = "<span>{$LANGDATA['L_NEWS_RELATED']}:</span>";
        foreach ($news_related as $related) {
            $related_content .= "<li><a rel='nofollow' target='_blank' href='{$related['link']}'>{$related['link']}</a></li>";
        }
        $tpl->addto_tplvar("NEWS_RELATED", $related_content);
    }
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "news_show_body"));                                               
}

function news_get_page($nid, $lang) {
    global $config, $tpl;    

     //TODO errors to within get_news_byID function
    if ( ($news_row = get_news_byId($nid, $config['WEB_LANG'])) == 403)  {
        //$tpl->addto_tplvar("E_MSG", $LANGDATA['L_ERROR_NOACCESS']);
        $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box", "L_ERROR_NOACCESS")); 
        return false; 
    } else if ($news_row == false) {
        if( ($news_row = get_news_byId($nid)) == false) {
            $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box", "L_NEWS_NOT_EXIST"));
            return false;
        } else {
            $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box", "L_NEWS_WARN_NOLANG"));
            return false;            
        }        
    }  
    if ($config['NEWS_MODERATION'] && $news_row['moderation'] && !S_GET_INT("admin") ) {
            $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box", "L_NEWS_ERROR_WAITINGMOD"));
            return false;        
    }     

    return $news_row;
}

function news_process_admin_actions() {
    global $acl_auth, $sm;
    
    //if we enter with &admin=1 already passing the admin check in news_show_page
    if (defined("ACL") && !S_GET_INT("admin") ) { 
        if(!$acl_auth->acl_ask("admin_all || news_admin")) { 
           return false;
        }
    } else if (!defined("ACL") && !S_GET_INT("admin")) {
        if ( ($user = $sm->getSessionUser()) == false || $user['isAdmin'] != 1) {
            return false;
        }        
    }                                    
    if (!empty($_GET['news_delete']) && !empty($_GET['lang_id']) &&
        $_GET['news_delete'] > 0 && $_GET['lang_id'] > 0) {
        news_delete(S_GET_INT("news_delete"), S_GET_INT("lang_id"));
        news_redirect();
    }
    if (!empty($_GET['news_approved']) && !empty($_GET['lang_id']) &&
        $_GET['news_approved'] > 0 && $_GET['lang_id'] > 0) {
        news_approved(S_GET_INT("news_approved"), S_GET_INT("lang_id"));             
    }
    if (isset($_GET['news_featured']) && !empty($_GET['lang_id'] && !empty($_GET['nid']))) {
        empty($_GET['news_featured']) ? $news_featured = 0: $news_featured = 1;                
        news_featured(S_GET_INT("nid", 11, 1), $news_featured, S_GET_INT("lang_id"));
    }
    if ( isset($_GET['news_frontpage']) && !empty($_GET['lang_id']) ) {
        news_frontpage( S_GET_INT("nid", 11, 1), S_GET_INT("lang_id"), S_GET_INT("news_frontpage", 1, 1));  
    }             
}
 
function news_nav_options($news) {
    global $LANGDATA, $config, $sm, $acl_auth;
    $content = "";
    $user = $sm->getSessionUser();
    // EDIT ADMIN, AUTHOR or Translator
    if( (defined('ACL') && $acl_auth->acl_ask("admin_all||news_admin")) 
            || (!defined('ACL') && $user['isAdmin'] == 1) 
            || ($news['author'] == $user['username']) 
            || (!empty($news['translator']) && ($news['translator'] == $user['username']))
    ) {            
        $content .= "<li><a href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&newsedit={$news['nid']}&lang_id={$news['lang_id']}'>{$LANGDATA['L_NEWS_EDIT']}</a></li>";
    }
    // TRANSLATE ADMIN, ANON IF, REGISTERED IF
    if( $config['NEWS_ANON_TRANSLATE']
            ||  (defined('ACL') && $acl_auth->acl_ask("admin_all||news_admin"))
            ||  (defined('ACL') && $config['NEWS_TRANSLATE_REGISTERED'] && $acl_auth->acl_ask("registered_all"))
            ||  (!defined('ACL') && $user['isAdmin'] == 1)
            ||  (!defined('ACL') && $config['NEWS_TRANSLATE_REGISTERED'] && !empty($user)) //NO_ACL registered
    ) {        
        $content .= "<li><a href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_new_lang={$news['nid']}&lang_id={$news['lang_id']}'>{$LANGDATA['L_NEWS_NEWLANG']}</a></li>";
    }
    //REST ONLY ADMIN
    if( (defined('ACL') && $acl_auth->acl_ask("admin_all||news_admin"))
        || (!defined('ACL') && $user['isAdmin'] == 1) 
    ) {
        if ($news['featured'] == 1) {
            $content .= "<li><a class='link_active' href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_featured=0&featured_value=0&lang_id={$news['lang_id']}&admin=1''>{$LANGDATA['L_NEWS_FEATURED']}</a></li>";    
        } else {
            $content .= "<li><a href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_featured=1&featured_value=1&lang_id={$news['lang_id']}&admin=1''>{$LANGDATA['L_NEWS_FEATURED']}</a></li>";    
        }
        if ($news['moderation']) {
            $content .= "<li><a href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_approved={$news['nid']}&lang_id={$news['lang_id']}&admin=1'>{$LANGDATA['L_NEWS_APPROVED']}</a></li>";
        }        
        //TODO  Add a menu for enable/disable news
        //$content .= "<li><a href=''>{$LANGDATA['L_NEWS_DISABLE']}</a></li>";
        $content .= "<li><a href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_delete={$news['nid']}&lang_id={$news['lang_id']}&admin=1&return_home=1' onclick=\"return confirm('{$LANGDATA['L_NEWS_CONFIRM_DEL']}')\">{$LANGDATA['L_NEWS_DELETE']}</a></li>";        
        if ($config['NEWS_SELECTED_FRONTPAGE'] ){
            if ($news['frontpage'] == 1) {
                $content .= "<li><a class='link_active' href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_frontpage=0&lang_id={$news['lang_id']}'>{$LANGDATA['L_NEWS_FRONTPAGE']}</a></li>";        
            } else {
                $content .= "<li><a href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_frontpage=1&lang_id={$news['lang_id']}'>{$LANGDATA['L_NEWS_FRONTPAGE']}</a></li>";
            }
        }
    }
    return $content;
}

function news_delete($nid, $lang_id) {
    global $db;
    
    if (empty($nid) || empty($lang_id)) {
        return false;
    }
    $db->delete("news", array("nid" => $nid, "lang_id" => $lang_id));
    
    $query = $db->select_all("news", array("nid" => $nid));
    if ($db->num_rows($query) <= 0) {
        $db->delete("links", array("plugin" => "Newspage", "source_id" => $nid));
    }
    return true;
}

function news_approved($nid, $lang_id) {
    global $db;
    
    if (empty($nid) || empty($lang_id) ) {
        return false;
    }    
    $db->update("news", array("moderation" => 0), array("nid" => $nid, "lang_id" => $lang_id));        

    return true;    
}

function news_featured($nid, $featured, $lang_id) {
    global $db;
    
    if (empty($nid) || empty($lang_id)) {
        return false;
    }
    $featured == 1 ? news_clean_featured($lang_id) : false;
    $db->update("news", array("featured" => $featured), array("nid" => $nid, "lang_id" => $lang_id));

    return true;    
}

function news_frontpage($nid, $lang_id, $frontpage_state) {
    global $db;

    empty($frontpage_state) ? $frontpage_state = 0 : false;

    if (empty($nid) || empty($lang_id) || $nid <= 0 && $lang_id <= 0) {            
        return false;
    }
    $db->update("news", array("frontpage" => $frontpage_state), array("nid" => $nid, "lang_id" => $lang_id));
    
    return true;    
}

function news_redirect()  {
    global $config;
    
    if(!empty($_GET['return_home'])) {
        header("Location: /{$config['WEB_LANG']} ");                
    } else {        
        //header("Location: {$_SERVER['HTTP_REFERER']} ");  //TODO FILTER
        header("Location: ". S_SERVER_URL("HTTP_REFERER")."/{$config['WEB_LANG']} ");
    }    
}