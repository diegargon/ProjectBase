<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_new_page() {
    global $acl_auth, $sm, $tpl, $LANGDATA;

    $nid = S_GET_INT("nid", 11, 1);
    $lang_id = S_GET_INT("lang_id", 4, 1);

    if (empty($nid) || empty($lang_id)) {
        return news_error_msg("L_NEWS_NOT_EXIST");
    }
    if (!($news_data = get_news_byId($nid, $lang_id, 1))) { //get first page
        return false; // error already setting in get_news
    }

    if (!($user = $sm->getSessionUser())) {
        return news_error_msg("L_E_NOACCESS");
    }

    $user['uid'] > 0 ? $form_data['tos_checked'] = 1 : false;

    if (( $news_data['author_id'] == $user['uid']) || (defined('ACL') && $acl_auth->acl_ask("news_admin||admin_all")) || (!defined('ACL') && $user['isAdmin'])
    ) {
        //Do nothing
    } else {
        return news_error_msg("L_E_NOACCESS");
    }

    $form_data['news_form_title'] = $LANGDATA['L_NEWS_CREATE_NEW_PAGE'];
    $form_data['can_change_author'] = "disabled";
    $form_data['author'] = $user['username'];
    $form_data['news_text_bar'] = news_editor_getBar();
    $form_data['new_page'] = 1;
    do_action("news_newpage_form_add");

    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "news_form", $form_data));
}

function news_newpage_form_process() {
    global $LANGDATA, $config;

    $news_data = news_form_getPost();

    if ($news_data['title'] == false) {
        die('[{"status": "3", "msg": "' . $LANGDATA['L_NEWS_TITLE_ERROR'] . '"}]');
    }
    if ((strlen($news_data['title']) > $config['NEWS_TITLE_MAX_LENGHT']) ||
            (strlen($news_data['title']) < $config['NEWS_TITLE_MIN_LENGHT'])
    ) {
        die('[{"status": "3", "msg": "' . $LANGDATA['L_NEWS_TITLE_MINMAX_ERROR'] . '"}]');
    }
    if (!empty($news_data['lead']) && (strlen($news_data['lead']) > $config['NEWS_LEAD_MAX_LENGHT'])) {
        die('[{"status": "4", "msg": "' . $LANGDATA['L_NEWS_LEAD_MINMAX_ERROR'] . '"}]');
    }
    if ($news_data['text'] == false) {
        die('[{"status": "5", "msg": "' . $LANGDATA['L_NEWS_TEXT_ERROR'] . '"}]');
    }
    if ((strlen($news_data['text']) > $config['NEWS_TEXT_MAX_LENGHT']) ||
            (strlen($news_data['text']) < $config['NEWS_TEXT_MIN_LENGHT'])
    ) {
        die('[{"status": "5", "msg": "' . $LANGDATA['L_NEWS_TEXT_MINMAX_ERROR'] . '"}]');
    }
    if (empty($news_data['lang_id']) || empty($news_data['nid'])) {
        die('[{"status": "8", "msg": "' . $LANGDATA['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }
    if (news_newpage_submit_new($news_data)) {
        die('[{"status": "ok", "msg": "' . $LANGDATA['L_NEWS_UPDATE_SUCESSFUL'] . '", "url": "' . $config['WEB_URL'] . '"}]');
    } else {
        die('[{"status": "1", "msg": "' . $LANGDATA['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }

    return true;
}

function news_newpage_submit_new($news_data) {
    global $db, $config;

    $query = $db->select_all("news", array("nid" => "{$news_data['nid']}", "lang_id" => "{$news_data['lang_id']}"), "ORDER BY page");

    if (($num_pages = $db->num_rows($query)) <= 0) {
        return news_error_msg("L_NEWS_NOT_EXIST");
    }
    $news_father = $db->fetch($query);

    $insert_ary = array(
        "nid" => $news_father['nid'],
        "lang_id" => $news_father['lang_id'],
        "title" => $news_data['title'],
        "text" => $news_data['text'],
        "featured" => $news_father['featured'],
        "author" => $news_father['author'],
        "author_id" => $news_father['author_id'],
        "category" => $news_father['category'],
        "lang" => $news_father['lang'],
        "acl" => $news_father['acl'],
        "moderation" => $config['NEWS_MODERATION'],
        "page" => ++$num_pages
    );
    !empty($news_data['lead']) ? $insert_ary['lead'] = $news_data['lead'] : false;
    $db->insert("news", $insert_ary);

    return true;
}
