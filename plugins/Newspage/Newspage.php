<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Newspage_init(){  
    
    if ('DEBUG_PLUGINS_LOAD' && 'DEBUG') { print_debug("Newspage Inititated<br/>");}
    
    includePluginFiles("Newspage"); 
    getCSS_filePath("Newspage");
    getCSS_filePath("Newspage", "Newspage-mobile");  

    if (news_check_display_submit()) {
        register_action("nav_element", "news_menu_submit_news");
    }    
}

function news_index_page (){       

    if(!empty($_GET['sendnews']) && empty($_POST['newsFormSubmit']) ) { // && empty ($_POST['newsFormSubmit_ST2'])) {
        require_once 'includes/news_form.common.php';
        require_once("includes/news_form.submit.php");
        if (empty($_POST['newsFormSubmit_ST2'])) {
            do_action("common_web_structure");
            addto_tplvar("SCRIPTS", Newspage_FormScript());            
            news_new_form();
        } else {
            news_form_process();
        }
    } else {
        require_once("includes/news.portal.php");
        do_action("common_web_structure");       
        news_portal();
    }
}

function news_page() {
    global $acl_auth;        

    if(!empty($_GET['newsedit']) && !empty($_GET['lang_id'])) {
        //TODO ADD AUTHOR EDITING
        if(defined('ACL') && 'ACL') { 
            if ($acl_auth->acl_ask("admin_all||news_admin")) { // || $acl_auth->acl_ask("news_admin")) {
                //TODO Check if its the author or ACL rights 
                require_once 'includes/news_form.common.php';
                require_once 'includes/news_page_edit.php';        
                if (!empty($_POST['news_update']) && !empty($_POST['newsFormSubmit_ST2'])) {
                    news_form_process();
                } else {            
                    do_action("common_web_structure");
                    addto_tplvar("SCRIPTS", Newspage_FormScript());
                    news_page_edit();              
                }
            } else {                
                $GLOBALS['tpldata']['E_MSG'] = $GLOBALS['LANGDATA']['L_NEWS_NO_EDIT_PERMISS'];
                do_action("error_message_page");
            }
        }
    } else {
        require_once("includes/news_page.main.php");   
        do_action("common_web_structure");
        news_page_main();
    }
}