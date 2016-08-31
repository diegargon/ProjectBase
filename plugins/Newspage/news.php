<?php

/*
 *  Copyright @ 2016 Diego Garcia	
 */
!defined('IN_WEB') ? exit : true;

do_action("news_page_begin");

!empty($_POST['preview']) ? news_form_preview() : null;

if (!empty($_GET['newsedit']) && !empty($_GET['lang_id']) && empty($_POST['preview'])) {
    do_action("begin_newsedit");
    require_once ("includes/news_form.common.php");
    require_once ("includes/news_page_edit.php");

    if (!empty($_POST['news_update'])) {
        news_form_edit_process();
    } else {
        do_action("common_web_structure");
        !empty($_GET['npage']) && $_GET['npage'] > 1 ? Newspage_FormPageScript() : Newspage_FormScript();
        news_edit();
    }
} else if (defined('MULTILANG') && !empty($_GET['news_new_lang']) && empty($_POST['preview'])) {
    do_action("begin_news_new_lang");
    require_once ("includes/news_form.common.php");
    require_once ("includes/news_new_lang.php");
    if (!empty($_POST['post_newlang'])) {
        news_form_newlang_process();
    } else {
        do_action("common_web_structure");
        Newspage_FormScript();
        news_new_lang();
    }
} else if (!empty($_GET['newpage']) && empty($_POST['preview'])) {
    do_action("begin_newspage");
    require_once ("includes/news_form.common.php");
    require_once ("includes/news_new_page.php");
    if (!empty($_POST['new_page'])) {
        news_newpage_form_process();
    } else {
        do_action("common_web_structure");
        Newspage_FormPageScript();
        news_new_page();
    }
} else if (empty($_POST['preview'])) {
    do_action("begin_newsshow");
    require_once("includes/news_page.main.php");
    require_once("includes/parser.class.php");
    do_action("common_web_structure");
    news_show_page();
}
