<?php
/*
	
*/
if (!defined('IN_WEB')) { exit; }

do_action("news_page_begin");

if (!empty($_GET['newsedit']) && !empty($_GET['lang_id'])) {
    do_action("begin_newsedit");
    require_once ("includes/news_form.common.php");
    require_once ("includes/news_page_edit.php");

    if (($news_data = news_check_edit_authorized()) != false) {
        if (!empty($_POST['newsFormSubmit_ST2'])) {
            news_form_process($news_data['news_auth']);
        } else if (!empty($_POST['preview'])) {
            news_form_preview();
        } else {
            do_action("common_web_structure");
            if (!empty($_GET['npage']) && $_GET['npage'] > 1) {
                Newspage_FormPageScript();
            } else {
                Newspage_FormScript();
            }
            news_edit($news_data);
        }
    } else {
        do_action("common_web_structure"); // error messsage box already set
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
    if (!empty($_POST['num_pages']) && empty($_POST['preview'])) {
        news_newpage_form_process();
    } else if (!empty($_POST['preview'])) {
        news_form_preview();
    } else {
        do_action("common_web_structure");
        Newspage_FormPageScript();
        news_new_page();
    }
} else {
    require_once("includes/news_page.main.php");
    require_once("includes/parser.class.php");
    do_action("common_web_structure");
    news_show_page();
}
