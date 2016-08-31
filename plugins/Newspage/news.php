<?php
/*
 *  Copyright @ 2016 Diego Garcia	
*/
if (!defined('IN_WEB')) { exit; }

do_action("news_page_begin");

if (!empty($_GET['newsedit']) && !empty($_GET['lang_id'])) {
    do_action("begin_newsedit");
    require_once ("includes/news_form.common.php");
    require_once ("includes/news_page_edit.php");

    if (!empty($_POST['news_update']) && empty($_POST['preview'])) {
        news_form_edit_process();
    } else if (!empty($_POST['preview'])) {
        news_form_preview();
    } else {
        do_action("common_web_structure");
        if (!empty($_GET['npage']) && $_GET['npage'] > 1) {
            Newspage_FormPageScript();
        } else {
            Newspage_FormScript();
        }
        news_edit();
    }
} else if (!empty($_GET['news_new_lang'])) {
    do_action("begin_news_new_lang");
    require_once ("includes/news_form.common.php");
    require_once ("includes/news_new_lang.php");
    if (defined('MULTILANG') && !empty($_POST['post_newlang']) && empty($_POST['preview'])) {
        news_form_newlang_process();
    } else if (!empty($_POST['preview'])) {
        news_form_preview();
    } else if (defined('MULTILANG')) {
        do_action("common_web_structure");
        Newspage_FormScript();
        news_new_lang();
    }
} else if (!empty($_GET['newpage'])) {
    do_action("begin_newspage");
    require_once ("includes/news_form.common.php");
    require_once ("includes/news_new_page.php");
    if (!empty($_POST['new_page']) && empty($_POST['preview'])) {
        news_newpage_form_process();
    } else if (!empty($_POST['preview'])) {
        news_form_preview();
    } else {
        do_action("common_web_structure");
        Newspage_FormPageScript();
        news_new_page();
    }
} else {
    do_action("begin_newsshow");
    require_once("includes/news_page.main.php");
    require_once("includes/parser.class.php");
    do_action("common_web_structure");
    news_show_page();
}
