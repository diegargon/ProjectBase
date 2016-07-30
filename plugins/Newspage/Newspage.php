<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Newspage_init(){
    global $tpl;
    print_debug("Newspage Inititated", "PLUGIN_LOAD");

    includePluginFiles("Newspage");
    $tpl->getCSS_filePath("Newspage");
    $tpl->getCSS_filePath("Newspage", "Newspage-mobile");

    if (news_check_display_submit()) {
        register_action("nav_element", "news_menu_submit_news");
    }
}

function news_index_page (){
    global $tpl;

    if(!empty($_GET['sendnews']) && empty($_POST['newsFormSubmit']) ) {
        require_once 'includes/news_form.common.php';
        require_once("includes/news_form.submit.php");

        if (!empty($_POST['newsFormSubmit_ST2']) ) {
            news_form_process("admin"); //if author admin options ins't submited
        } else if(!empty($_POST['preview'])) {
           news_form_preview();
        } else {
            do_action("common_web_structure");
            $tpl->addto_tplvar("SCRIPTS_BOTTOM", Newspage_FormScript());
            news_form_new();
        }
    } else {
        require_once("includes/news.portal.php");
        do_action("common_web_structure");
        news_portal();
    }
}

function news_page() {
    global $tpl;       

    if(!empty($_GET['newsedit']) && !empty($_GET['lang_id'])) {
        require_once ("includes/news_form.common.php");
        require_once ("includes/news_page_edit.php");

        if( ($news_data = news_check_edit_authorized()) != false) {
            if (!empty($_POST['newsFormSubmit_ST2'])) {
                news_form_process($news_data['news_auth']);
            } else if (!empty($_POST['preview'])) {
                news_form_preview();
            } else {
                do_action("common_web_structure");
                $tpl->addto_tplvar("SCRIPTS_BOTTOM", Newspage_FormScript());
                news_edit($news_data);
            }
        } else {
            do_action("common_web_structure"); // error messsage box already set
        }
    } else if (!empty($_GET['news_new_lang'])) {
        require_once ("includes/news_form.common.php");
        require_once ("includes/news_new_lang.php");
        if (defined('MULTILANG') && !empty($_POST['post_newlang'])) {
            news_form_newlang_process();
        } else if (defined('MULTILANG')) {
            do_action("common_web_structure");
            $tpl->addto_tplvar("SCRIPTS_BOTTOM", Newspage_FormScript());
            news_new_lang();
        }
    } else if (!empty($_GET['newpage'])) {
        require_once ("includes/news_form.common.php");
        require_once ("includes/news_new_page.php");
        if(!empty($_POST['num_pages']) && empty($_POST['preview'])) {
            news_newpage_form_process();
        } else if (!empty($_POST['preview'])) {
            news_form_preview ();
        } else {
            do_action("common_web_structure");
            $tpl->addto_tplvar("SCRIPTS_BOTTOM", Newspage_FormScript());
            news_new_page();
        }
    } else {
        require_once("includes/news_page.main.php");
        require_once("includes/parser.class.php");
        do_action("common_web_structure");
        news_show_page();
    }
}