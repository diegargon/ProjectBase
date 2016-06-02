<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Newspage_init(){  
    
    if (DEBUG_PLUGINS_LOAD) { print_debug("Newspage Inititated<br/>");}
    
    includePluginFiles("Newspage"); 
    getCSS_filePath("Newspage");
    getCSS_filePath("Newspage", "Newspage-mobile");  

    if (news_check_display_submit()) {
        register_action("nav_element", "news_menu_submit_news");
    }    
}

function news_index_page (){       
    
    if(!empty($_GET['sendnews']) && empty($_POST['sendnews'])  && empty($_POST['sendnews_stage2'])) {
        require_once("includes/news_sendform.inc.php");
        do_action("common_web_structure");
        addto_tplvar("SCRIPTS", Newspage_SendNewsScript());
        news_display_submit_news();
    }  else if(!empty($_POST['sendnews'])) {
            require_once("includes/news_sendform.inc.php");
            do_action("common_web_structure");
            addto_tplvar("SCRIPTS", Newspage_SendNewsScript());
            $post_data = news_sendnews_getPost();
            news_display_submit_news($post_data);  
    } else if (!empty($_POST['sendnews_stage2'])) {
        require_once("/includes/news_sendform.inc.php");
        news_form_submit_process();
    } else {
        do_action("common_web_structure");
        news_portal();
    }
}

function news_portal() {
    global $tpldata, $config;
        
    $news_nLayout = news_layout_select();
    $news_layout_tpl = "news_body_style" . $news_nLayout++;
    
    if ($config['LAYOUT_SWITCH']) {           
        $tpldata['news_nSwitch'] = $news_nLayout;
        register_action("nav_element", "news_layout_switcher", 6);
    }
    $tpldata['FEATURED'] = get_news_featured();
    $tpldata['COL1_ARTICLES'] = get_news(1,0);
    $tpldata['COL2_ARTICLES'] = get_news(2,0);
    $tpldata['COL3_ARTICLES'] = get_news(1,0);                  
    addto_tplvar("POST_ACTION_ADD_TO_BODY", getTPL_file("Newspage", $news_layout_tpl));     
}

function news_page() {
    global $tpldata, $config, $LANGDATA, $acl_auth;
    
    require_once("includes/news_page.inc.php");
    
    //TODO: Split simplified in functions -> news_page.inc

    
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
                header('Location: '. '/');
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

