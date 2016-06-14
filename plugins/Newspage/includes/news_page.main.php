<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function news_page_main() {
    global $config, $LANGDATA, $acl_auth, $tpl;
    
    //TODO: Split/simplified in functions
    
    if( (empty($_GET['nid'])) || ($nid = S_VAR_INTEGER($_GET['nid'], 8, 1)) == false) {
        $tpl->addto_tplvar("E_MSG", $LANGDATA['L_NEWS_NOT_EXIST']);
        $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box"));
        return false;
    }
    if ( S_GET_INT("admin") && ( $newslang = S_GET_CHAR_AZ("newslang", 2, 1) ) ) {
       if (defined("ACL") && "ACL") {
           if($acl_auth->acl_ask("admin_all||news_admin")){// || $acl_auth->acl_ask("news_admin")) {
               //Do nothing
            } else {        
               $tpl->addto_tplvar("E_MSG", $LANGDATA['L_ERROR_NOACCESS']);
               $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box")); 
               return false;                
           }
       }
       if ( (($news_row = get_news_byId($nid, $newslang)) == 403) )  {
           $tpl->addto_tplvar("E_MSG", $LANGDATA['L_ERROR_NOACCESS']);
           $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box")); 
           return false; 
       }       
    } else if ( ($news_row = get_news_byId($nid, $config['WEB_LANG'])) == 403)  {
        $tpl->addto_tplvar("E_MSG", $LANGDATA['L_ERROR_NOACCESS']);
        $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box")); 
        return false; 
    } else if ($news_row == false) {
        if( ($news_row = get_news_byId($nid)) == false) {
            $tpl->addto_tplvar("E_MSG", $LANGDATA['L_NEWS_NOT_EXIST']);
            $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box"));
            return false;
        } else {
            $tpl->addto_tplvar("E_MSG", $LANGDATA['L_NEWS_WARN_NOLANG']);
            $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box"));
            return false;            
        }        
    }  
    if ($config['NEWS_MODERATION'] && $news_row['moderation'] && !S_GET_INT("admin") ) {
            $tpl->addto_tplvar("E_MSG", $LANGDATA['L_NEWS_ERROR_WAITINGMOD']);
            $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box"));
            return false;        
    }     
    //ADMIN
    if (defined("ACL") && "ACL") {
        if($acl_auth->acl_ask("admin_all || news_admin")) { // || $acl_auth->acl_ask("news_admin")) {
            $tpl->addto_tplvar("NEWS_ADMIN_NAV", Newspage_AdminOptions($news_row));
            
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
    $tpl_data['NID'] = $news_row['nid'];    
    $tpl_data['NEWS_TITLE'] = $news_row['title'];    
    $tpl_data['NEWS_LEAD'] = $news_row['lead'];    
    $tpl_data['NEWS_URL'] = "news.php?nid=$news_row[nid]";
    $tpl_data['NEWS_DATE'] = format_date($news_row['date']);
    $tpl_data['NEWS_AUTHOR'] = $news_row['author'];
    $tpl_data['NEWS_AUTHOR_UID'] = $news_row['author_id'];       
    $tpl_data['NEWS_TEXT']  = $news_row['text'];
    $tpl->addtpl_array($tpl_data);
    
    if ( ($allmedia = get_news_media_byID($nid)) != false) {
        foreach ($allmedia as $media) {
            if($media['itsmain'] == 1 ) {
                 $tpl->addto_tplvar("NEWS_MAIN_MEDIA", news_format_media($media));
            }
        }
    }    
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "news_show_body"));                                               
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
    $content .= "</ul>";
    $content .= "</nav>";
    $content .= "</div>";

    return $content;
}

function news_delete($nid, $lang_id) {
    global $db;
    
    if (!empty($nid) && !empty($lang_id)) {    
        $db->delete("news", array("nid" => $nid, "lang_id" => $lang_id));
        $db->delete("links", array("plugin" => "Newspage", "source_id" => $nid));
    } else {
        return false;
    }
    return true;
}

function news_approved($nid, $lang_id) {
    global $db;
    
    if (!empty($nid) && !empty($lang_id) ) {           
        $db->update("news", array("moderation" => 0), array("nid" => $nid, "lang_id" => $lang_id));        
    } else {
        return false;
    }
    return true;    
}

function news_featured($nid, $featured, $lang_id) {
    global $db;
    
    if (!empty($nid) && !empty($lang_id)) {            
        $db->update("news", array("featured" => $featured), array("nid" => $nid, "lang_id" => $lang_id));
        if($featured == 1) {
            $db->update("news", array("featured" => 0), array("lang_id" => $lang_id, "nid" => array("value" => $nid, "operator" => "!=" ) ));
        }
    } else {
        return false;
    }
    return true;    
}

function news_frontpage($nid, $lang_id, $frontpage_state) {
    global $db;

    if (empty($frontpage_state)) {
        $frontpage_state = 0;
    }
    if (!empty($nid) && isset($frontpage_state) && !empty($lang_id) && $nid > 0 && $lang_id > 0) {            
        $db->update("news", array("frontpage" => $frontpage_state), array("nid" => $nid, "lang_id" => $lang_id));
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
