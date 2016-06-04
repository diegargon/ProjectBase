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
        require_once("includes/news.portal.php");
        do_action("common_web_structure");       
        news_portal();
    }
}

function news_page() {
    global $acl_auth;
    
    require_once("includes/news_page.inc.php");   

    news_page_main();
}