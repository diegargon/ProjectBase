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
        do_action("common_web_structure");
        addto_tplvar("SCRIPTS", Newspage_SendNewsScript());
        news_display_submit_news();
    }  else if(!empty($_POST['sendnews'])) {
            do_action("common_web_structure");
            addto_tplvar("SCRIPTS", Newspage_SendNewsScript());
            $post_data = news_sendnews_getPost();
            news_display_submit_news($post_data);  
    } else if (!empty($_POST['sendnews_stage2'])) {
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
    global $tpldata, $config, $LANGDATA;

    if( ($nid = S_VAR_INTEGER($_GET['nid'], 8, 1)) == false) {
        $tpldata['E_MSG'] = $LANGDATA['L_NEWS_NOT_EXIST'];
        addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box"));
        return false;
    }
    if (($row = get_news_byId($nid, $config['WEB_LANG'])) == 403) {
        $tpldata['E_MSG'] = $LANGDATA['L_ERROR_NOACCESS'];
        addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box")); 
        return false; 
    } else if ($row == false) {
        if( ($row = get_news_byId($nid)) == false) {
            $tpldata['E_MSG'] = $LANGDATA['L_NEWS_NOT_EXIST'];
            addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box"));
            return false;
        } else {
            $tpldata['E_MSG'] = $LANGDATA['L_NEWS_WARN_NOLANG'];
            addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box"));
            return false;
            
        }        
    }    
    $tpldata['NID'] = $row['nid'];    
    $tpldata['NEWS_TITLE'] = $row['title'];    
    $tpldata['NEWS_LEAD'] = $row['lead'];    
    $tpldata['NEWS_URL'] = "news.php?nid=$row[nid]";
    $tpldata['NEWS_DATE'] = format_date($row['date']);
    $tpldata['NEWS_AUTHOR'] = $row['author'];
    $tpldata['NEWS_TEXT']  = $row['text'];

    if ( ($allmedia = get_news_media_byID($nid)) != false) {
        foreach ($allmedia as $media) {
            if($media['itsmain'] == 1 ) {
                $tpldata['NEWS_MAIN_MEDIA'] = news_format_media($media);
            }
        }
    }
    addto_tplvar("POST_ACTION_ADD_TO_BODY", getTPL_file("Newspage", "news_show_body"));                                               
}

function news_display_submit_news($post_data = null) {
    global $config, $LANGDATA, $acl_auth;
    
    if (  isset($_SESSION['isLogged']) && S_VAR_INTEGER($_SESSION['isLogged'])   == 1) {
        $user = SMBasic_getUserbyID(S_VAR_INTEGER($_SESSION['uid']), 11);        
    } else {
        $user['username'] = $LANGDATA['L_ANONYMOUS'];
    }
    $data['username'] = $user['username'];    
    if (defined('MULTILANG') && 'MULTILANG') {
        $LANGS = do_action("get_site_langs"); //Provided by ML
        $select = "<select name='news_lang' id='news_lang'>";     
        foreach ($LANGS as $content) {
            if($content->iso_code == $config['WEB_LANG']) {
                $select .= "<option selected value='$content->iso_code'>$content->lang_name</option>";
            } else {
                $select .= "<option value='$content->iso_code'>$content->lang_name</option>";
            }
        }        
        $select .= "</select>";
        $data['select_langs'] = $select;
    }    
    if (defined('ACL') && 'ACL') {        
        if($acl_auth->acl_ask("news_admin") || $acl_auth->acl_ask("admin_all")) {
            $data['select_acl'] = $acl_auth->get_roles_select("news");
        }
    }    
    $data['select_categories'] = news_get_categories_select();    
    if (defined('ACL')) {
        if( 
                ( $acl_auth->acl_ask('news_admin') ) == true ||
                ( $acl_auth->acl_ask('admin_all') ) == true ) 
            {
            $can_change_author = 1;
            }
    }       
    empty($can_change_author) ?  $data['can_change_author'] = "disabled" : $data['can_change_author'] = "";    
    if(!empty($post_data)) {
        $data = array_merge($data, $post_data);
    }  
    addto_tplvar("POST_ACTION_ADD_TO_BODY", getTPL_file("Newspage", "news_submit", $data));     
}

