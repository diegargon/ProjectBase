<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_new_form() {
    global $LNG, $cfg, $acl_auth, $tpl, $sm;

    $form_data['news_form_title'] = $LNG['L_CREATE_NEWS'];

    $user = $sm->getSessionUser();
    if (empty($user) && $cfg['NEWS_SUBMIT_ANON']) {
        $form_data['author'] = $LNG['L_NEWS_ANONYMOUS'];
        $form_data['tos_checked'] = 0;
    } else if (empty($user)) {
        return news_error_msg("L_E_NOACCESS");
    }
    $user ? $form_data['author'] = $user['username'] : null;
    $user ?$form_data['tos_checked'] = 1 : null;
    !empty($user) ? $news_data['tos_checked'] = 1 : $news_data['tos_checked'] = 0;

    if (defined('MULTILANG') && ($site_langs = news_get_all_sitelangs()) != false) {
        $form_data['select_langs'] = $site_langs;
    }
    if ($user && defined('ACL') && $acl_auth->acl_ask("news_admin||admin_all")) {
        $form_data['select_acl'] = $acl_auth->get_roles_select("news");
        $form_data['news_auth'] = "admin";
    } else {
        $form_data['can_change_author'] = "disabled";
    }
    if ($user && !defined('ACL') && $user['isAdmin']) {
        $form_data['news_auth'] = "admin";
    } else {
        $form_data['can_change_author'] = "disabled";
    }
    $form_data['select_categories'] = news_get_categories_select();
    $form_data['terms_url'] = $cfg['TERMS_URL'];
    do_action("news_new_form_add", $form_data);
    $form_data['news_text_bar'] = news_editor_getBar();
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "news_form", $form_data));
}

function news_create_new($news_data) {
    global $cfg, $ml, $db;

    $news_data['nid'] = $db->get_next_num("news", "nid");

    if (defined('MULTILANG')) {
        $lang_id = $ml->iso_to_id($news_data['lang']);
    } else {
        $lang_id = $cfg['WEB_LANG_ID'];
    }
    !empty($news_data['acl']) ? $acl = $news_data['acl'] : $acl = "";
    empty($news_data['featured']) ? $news_data['featured'] = 0 : null;

    if ($news_data['featured'] == 1 && $cfg['NEWS_MODERATION'] == 1) {
        $moderation = 0;
    } else if ($cfg['NEWS_MODERATION'] == 1) {
        $moderation = 1;
    } else {
        $moderation = 0;
    }

    $insert_ary = [
        "nid" => $news_data['nid'],
        "lang_id" => $lang_id,
        "page" => 1,
        "title" => $news_data['title'],
        "lead" => $news_data['lead'],
        "text" => $news_data['text'],
        "featured" => $news_data['featured'],
        "author" => $news_data['author'],
        "author_id" => $news_data['author_id'],
        "category" => $news_data['category'],
        "lang" => $news_data['lang'],
        "acl" => $acl,
        "moderation" => $moderation
    ];

    do_action("news_mod_submit_insert", $insert_ary);
    $db->insert("news", $insert_ary);

    /* Custom / MOD */
    do_action("news_create_new_insert", $news_data);

    $plugin = "Newspage";

    //SOURCE LINK
    if (!empty($news_data['news_source'])) {
        $type = "source";
        $insert_ary = [
            "source_id" => $news_data['nid'],
            "plugin" => $plugin,
            "type" => $type,
            "link" => $news_data['news_source']
        ];
        $db->insert("links", $insert_ary);
    }
    //NEW RELATED
    if (!empty($news_data['news_new_related'])) {
        $type = "related";
        $insert_ary = [
            "source_id" => $news_data['nid'], "plugin" => $plugin,
            "type" => $type, "link" => $news_data['news_new_related'],
        ];
        $db->insert("links", $insert_ary);
    }
    return true;
}

function news_form_submit_process() {
    global $LNG, $cfg, $sm;

    $user = $sm->getSessionUser();
    if (!$user && !$cfg['NEWS_SUBMIT_ANON']) {
        return false;
    }
    $news_data = news_form_getPost();

    if (news_form_common_field_check($news_data) == false) {
        return false;
    }
    if (news_form_extra_check($news_data) == false) {
        return false;
    }
    if (news_create_new($news_data)) {
        die('[{"status": "ok", "msg": "' . $LNG['L_NEWS_SUBMITED_SUCCESSFUL'] . '", "url": "' . $cfg['WEB_URL'] . '"}]');
    } else {
        die('[{"status": "1", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }
}
