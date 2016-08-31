<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_new_lang() {
    global $config, $LANGDATA, $acl_auth, $tpl, $db, $sm;

    $nid = S_GET_INT("nid", 11, 1);
    $lang_id = S_GET_INT("lang_id", 4, 1);
    $page = S_GET_INT("npage", 11, 1);

    if (empty($nid) || empty($lang_id) || empty($page)) {
        return news_error_msg("L_NEWS_NOT_EXIST");
    }
    if (!($news_data = get_news_byId($nid, $lang_id, $page))) {
        return false; // error already setting in get_news
    }

    $news_data['news_form_title'] = $LANGDATA['L_NEWS_NEWLANG'];

    $translator = $sm->getSessionUser();

    if (empty($translator) && $config['NEWS_ANON_TRANSLATE']) {
        $translator['username'] = $LANGDATA['L_NEWS_ANONYMOUS'];
        $translator['uid'] = 0;
    } else if (empty($translator)) {
        return news_error_msg("L_NEWS_NO_EDIT_PERMISS");
    }
    $news_data['translator'] = $translator['username'];
    $news_data['translator_id'] = $translator['uid'];
    $translator['uid'] > 0 ? $news_data['tos_checked'] = 1 : false;

    if ((defined('ACL') && !$acl_auth->acl_ask("news_admin||admin_all")) || ((!defined('ACL') && !$translator['isAdmin']))) {
        $news_data['can_change_author'] = "disabled";
    }

    if (($site_langs = news_get_missed_langs($news_data['nid'], $news_data['page'])) != false) {
        $news_data['select_langs'] = $site_langs;
    } else {
        return news_error_msg("L_NEWS_E_ALREADY_TRANSLATE_ALL");
    }
    $news_data['news_text_bar'] = news_editor_getBar();
    $news_data['new_lang'] = 1;
    do_action("news_newlang_form_add", $news_data);

    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "news_form", $news_data));
}

function news_form_newlang_process() {
    global $LANGDATA, $config;

    $news_data = news_form_getPost();

    if (news_form_common_field_check($news_data) == false) {
        return false;
    }

    if (news_translate($news_data)) {
        die('[{"status": "ok", "msg": "' . $LANGDATA['L_NEWS_TRANSLATE_SUCESSFUL'] . '", "url": "' . $config['WEB_URL'] . '"}]');
    } else {
        die('[{"status": "1", "msg": "' . $LANGDATA['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }
}

function news_translate($news_data) {
    global $config, $db, $ml;

    $lang_id = $ml->iso_to_id($news_data['lang']);

    if (empty($news_data['nid']) || empty($lang_id)) {
        return false;
    }

    $query = $db->select_all("news", array("nid" => "{$news_data['nid']}", "lang_id" => "$lang_id", "page" => "{$news_data['page']}"));
    if ($db->num_rows($query) > 0) { //already exist
        return false;
    }

    //GET original main news (page 1) for copy values
    $orig_news_nid = S_GET_INT("nid", 11, 1);
    $orig_news_lang = S_GET_CHAR_AZ("lang", 2, 2);
    $orig_news_lang_id = $ml->iso_to_id($orig_news_lang);

    $query = $db->select_all("news", array("nid" => "$orig_news_nid", "lang_id" => "$orig_news_lang_id", "page" => 1), "LIMIT 1");
    $orig_news = $db->fetch($query);

    $moderation = $config['NEWS_MODERATION'];

    $insert_ary = array(
        "nid" => $news_data['nid'],
        "lang_id" => $lang_id,
        "page" => $news_data['page'],
        "translator" => $news_data['news_translator'],
        "translator_id" => $news_data['news_translator_id'],
        "title" => $news_data['title'],
        "lead" => $news_data['lead'],
        "text" => $news_data['text'],
        "author" => $orig_news['author'],
        "author_id" => $orig_news['author_id'],
        "category" => $orig_news['category'],
        "lang" => $news_data['lang'],
        "acl" => $orig_news['acl'],
        "moderation" => $moderation
    );
    $db->insert("news", $insert_ary);

    return true;
}
