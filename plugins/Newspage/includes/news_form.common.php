<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_get_categories_select($news_data = null) {
    global $db, $acl_auth, $sm;

    $user = $sm->getSessionUser();

    if ($user && defined('ACL')) {
        $admin = $acl_auth->acl_ask("admin_all||news_admin");
    } else if ($user && !define('ACL')) {
        $admin = $user['isAdmin'];
    } else {
        $admin = false;
    }
    
    $query = news_get_categories();
    $select = "<select name='news_category' id='news_category'>";
    $fathers = [];
    while ($row = $db->fetch($query)) {
        $fathers_name = "";

        if (array_key_exists($row['father'], $fathers)) {
            $fathers[$row['cid']] = $fathers[$row['father']] . $row['name'] . "->";
        } else {
            $fathers[$row['cid']] = $row['name'] . "->";
        }
        $row['father'] ? $fathers_name = $fathers[$row['father']] : null;

        if (($row['admin'] == 1 && $admin == 1) || ($row['admin'] == 0)) {
            if (($news_data != null) && ($row['cid'] == $news_data['category']) && $row['father'] != 0) {
                $select .= "<option selected value='{$row['cid']}'>$fathers_name {$row['name']}</option>";
            } else if ($row['father'] != 0) {

                $select .= "<option value='{$row['cid']}'>$fathers_name {$row['name']}</option>";
            }
        }
    }
    $select .= "</select>";
    return $select;
}

function news_get_categories() {
    global $cfg, $ml, $db;

    if (defined('MULTILANG')) {
        $lang_id = $ml->getSessionLangId();
    } else {
        $lang_id = $cfg['WEB_LANG_ID'];
    }
    $query = $db->select_all("categories", ["plugin" => "Newspage", "lang_id" => "$lang_id"], "ORDER by father");

    return $query;
}

function news_form_getPost() {
    global $acl_auth, $sm, $LNG, $db;

    $user = $sm->getSessionUser();
    //Admin can change author (if the author not exists use admin one.
    if (($user && !defined('ACL') && $user['isAdmin']) || ( $user && defined('ACL') && ( $acl_auth->acl_ask('news_admin||admin_all') ) == true)) {
        if (($form_data['author'] = S_POST_STRICT_CHARS("news_author", 25, 3)) != false && ($form_data['author'] != $user['username'])) {
            if (($selected_user = $sm->getUserByUsername($form_data['author']))) {
                $form_data['author_id'] = $selected_user['uid'];
            } else {
                unset($form_data['author']); //clear use session username
            }
        }
    }

    if (empty($form_data['author']) || empty($form_data['author_id'])) {
        if (!empty($user)) {
            $form_data['author'] = $user['username'];
            $form_data['author_id'] = $user['uid'];
        } else {
            $form_data['author'] = $LNG['L_NEWS_ANONYMOUS'];
            $form_data['author_id'] = 0;
        }
    }

    $form_data['nid'] = S_GET_INT("nid", 11, 1);
    $form_data['lang_id'] = S_GET_INT("lang_id", 8, 1);
    $form_data['page'] = S_GET_INT("npage", 11, 1);
    $form_data['title'] = $db->escape_strip(S_POST_TEXT_UTF8("news_title"));
    $form_data['lead'] = $db->escape_strip(S_POST_TEXT_UTF8("news_lead"));
    $form_data['text'] = $db->escape_strip(S_POST_TEXT_UTF8("news_text"));
    $form_data['category'] = S_POST_INT("news_category", 8);
    $form_data['featured'] = S_POST_INT("news_featured", 1, 1);
    $form_data['lang'] = S_POST_CHAR_AZ("news_lang", 2);
    $form_data['acl'] = S_POST_STRICT_CHARS("news_acl");
    $form_data['news_source'] = S_POST_URL("news_source");
    $form_data['news_new_related'] = S_POST_URL("news_new_related");
    $form_data['news_related'] = S_POST_URL("news_related");
    $form_data['news_translator'] = S_POST_STRICT_CHARS("news_translator", 25, 3);
    $form_data['news_translator_id'] = S_POST_INT("news_translator_id", 11, 1);

    return $form_data;
}

function news_form_common_field_check($news_data) {
    global $cfg, $LNG;

    //USERNAME/AUTHOR
    if ($news_data['author'] == false) {
        die('[{"status": "2", "msg": "' . $LNG['L_NEWS_ERROR_INCORRECT_AUTHOR'] . '"}]');
    }
    //TITLE
    if ($news_data['title'] == false) {
        die('[{"status": "3", "msg": "' . $LNG['L_NEWS_TITLE_ERROR'] . '"}]');
    }
    if ((mb_strlen($news_data['title'], $cfg['CHARSET']) > $cfg['NEWS_TITLE_MAX_LENGHT']) ||
            (mb_strlen($news_data['title'], $cfg['CHARSET']) < $cfg['NEWS_TITLE_MIN_LENGHT'])
    ) {
        die('[{"status": "3", "msg": "' . $LNG['L_NEWS_TITLE_MINMAX_ERROR'] . '"}]');
    }
    //LEAD
    if (isset($_GET['npage']) && $_GET['npage'] > 1) {
        if ((mb_strlen($news_data['lead'], $cfg['CHARSET']) > $cfg['NEWS_LEAD_MAX_LENGHT'])) {
            die('[{"status": "4", "msg": "' . $LNG['L_NEWS_LEAD_MINMAX_ERROR'] . '"}]');
        }
    } else {
        if ($news_data['lead'] == false) {
            die('[{"status": "4", "msg": "' . $LNG['L_NEWS_LEAD_ERROR'] . '"}]');
        }
        if ((mb_strlen($news_data['lead'], $cfg['CHARSET']) > $cfg['NEWS_LEAD_MAX_LENGHT']) ||
                (mb_strlen($news_data['lead'], $cfg['CHARSET']) < $cfg['NEWS_LEAD_MIN_LENGHT'])
        ) {
            die('[{"status": "4", "msg": "' . $LNG['L_NEWS_LEAD_MINMAX_ERROR'] . '"}]');
        }
    }
    //TEXT
    if ($news_data['text'] == false) {
        die('[{"status": "5", "msg": "' . $LNG['L_NEWS_TEXT_ERROR'] . '"}]');
    }
    if ((mb_strlen($news_data['text'], $cfg['CHARSET']) > $cfg['NEWS_TEXT_MAX_LENGHT']) ||
            (mb_strlen($news_data['text'], $cfg['CHARSET']) < $cfg['NEWS_TEXT_MIN_LENGHT'])
    ) {
        die('[{"status": "5", "msg": "' . $LNG['L_NEWS_TEXT_MINMAX_ERROR'] . '"}]');
    }

    return true;
}

function news_form_extra_check(&$news_data) {
    global $cfg, $LNG;
    //CATEGORY
    if ($news_data['category'] == false) {
        die('[{"status": "1", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }
    //Source check valid if input
    if (!empty($_POST['news_source']) && $news_data['news_source'] == false && $cfg['NEWS_SOURCE']) {
        die('[{"status": "7", "msg": "' . $LNG['L_NEWS_E_SOURCE'] . '"}]');
    }
    //New related   check valid if input 
    if (!empty($_POST['news_new_related']) && $news_data['news_new_related'] == false && $cfg['NEWS_RELATED']) {
        die('[{"status": "7", "msg": "' . $LNG['L_NEWS_E_RELATED'] . '"}]');
    }
    //Old related  if input
    if (!empty($_POST['news_related']) && $news_data['news_related'] == false && $cfg['NEWS_RELATED']) {
        die('[{"status": "8", "msg": "' . $LNG['L_NEWS_E_RELATED'] . '"}]');
    }
    /* Custom /Mod Validators */
    if (($return = do_action("news_form_add_check", $news_data)) && !empty($return)) {
        die('[{"status": "9", "msg": "' . $return . '"}]');
    }
    //FEATURED NOCHECK ATM
    //ACL NO CHECK ATM

    return true;
}

function Newspage_FormScript() {
    global $tpl;

    $tpl->AddScriptFile("standard", "jquery", "TOP", null);
    $tpl->AddScriptFile("Newspage", "newsform", "BOTTOM");
    $tpl->AddScriptFile("Newspage", "editor", "BOTTOM");
}

//Used when submit new news, get all site available langs and selected the default/user lang
function news_get_all_sitelangs() {
    global $cfg, $ml;

    $site_langs = $ml->get_site_langs();

    if (empty($site_langs)) {
        return false;
    }

    $select = "<select name='news_lang' id='news_lang'>";
    foreach ($site_langs as $site_lang) {
        if ($site_lang['iso_code'] == $cfg['WEB_LANG']) {
            $select .= "<option selected value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
        } else {
            $select .= "<option value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
        }
    }
    $select .= "</select>";

    return $select;
}

//used when edit news, omit langs that already have this news translate
function news_get_available_langs($news_data) {
    global $cfg, $ml, $db;

    $site_langs = $ml->get_site_langs();
    if (empty($site_langs)) {
        return false;
    }

    empty($news_data['lang']) ? $match_lang = $news_data['lang'] : $match_lang = $cfg['WEB_LANG'];

    $select = "<select name='news_lang' id='news_lang'>";
    foreach ($site_langs as $site_lang) {
        if ($site_lang['iso_code'] == $match_lang) {
            $select .= "<option selected value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
        } else {
            $query = $db->select_all("news", ["nid" => $news_data['nid'], "lang_id" => $site_lang['lang_id']], "LIMIT 1");
            if ($db->num_rows($query) <= 0) {
                $select .= "<option value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
            }
        }
    }
    $select .= "</select>";

    return $select;
}

//used when translate a news, omit all already translate langs, exclude original lang too. just show langs without the news translate
function news_get_missed_langs($nid, $page) {
    global $ml, $db;

    $nolang = 1;

    $site_langs = $ml->get_site_langs();
    if (empty($site_langs)) {
        return false;
    }

    $select = "<select name='news_lang' id='news_lang'>";
    foreach ($site_langs as $site_lang) {
        $query = $db->select_all("news", ["nid" => $nid, "lang_id" => $site_lang['lang_id'], "page" => "$page"], "LIMIT 1");
        if ($db->num_rows($query) <= 0) {
            $select .= "<option value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
            $nolang = 0;
        }
    }
    $select .= "</select>";

    return (!empty($nolang)) ? false : $select;
}

function news_editor_getBar() {
    global $tpl;
    do_action("news_add_editor_item");

    return $tpl->getTPL_file("Newspage", "NewsEditorBar");
}

function news_form_preview() {
    global $db;
    require_once("parser.class.php");

    $news['news_text'] = $db->escape_strip(S_POST_TEXT_UTF8("news_text"));
    $news['news_text'] = stripcslashes($news['news_text']);
    !isset($news_parser) ? $news_parser = new parse_text : false;

    do_action("news_form_preview", $news);
    $content = $news_parser->parse($news['news_text']);

    echo $content;
}
