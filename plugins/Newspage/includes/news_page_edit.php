<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_edit() {
    global $config, $LANGDATA, $acl_auth, $tpl;

    $nid = S_GET_INT("nid", 11, 1);
    $lang_id = S_GET_INT("lang_id", 4, 1);
    $page = S_GET_INT("npage", 11, 1);

    if (empty($nid) || empty($lang_id) || empty($page)) {
        return news_error_msg("L_NEWS_NOT_EXIST");
    }
    if (!($news_data = get_news_byId($nid, $lang_id, $page))) {
        return false; // error already setting in get_news
    }
    if (!news_check_edit_authorized($news_data)) {
        return false; // error already setting in news_check....
    }
    $news_data['news_form_title'] = $LANGDATA['L_NEWS_EDIT_NEWS'];

    if ($news_data['news_auth'] == "admin") {
        defined('ACL') ? $news_data['select_acl'] = $acl_auth->get_roles_select("news", $news_data['acl']) : false;
    } else {
        $news_data['can_change_author'] = "disabled";
    }

    if ($news_data['news_auth'] == "admin" || $news_data['news_auth'] == "author") {
        $news_data['select_categories'] = news_get_categories_select($news_data);
        if (($news_source = get_news_source_byID($news_data['nid'])) != false) {
            $news_data['news_source'] = $news_source['link'];
        }
        if ($config['NEWS_RELATED'] && ($news_related = news_get_related($news_data['nid']))) {
            $news_data['news_related'] = "";
            foreach ($news_related as $related) {
                $news_data['news_related'] .= "<input type='text' class='news_link' name='news_related[{$related['link_id']}]' value='{$related['link']}' />\n";
            }
        }
    }
    if (defined('MULTILANG') && ($site_langs = news_get_available_langs($news_data)) != false) {
        $news_data['select_langs'] = $site_langs;
    }

    $news_data['news_text_bar'] = news_editor_getBar();
    do_action("news_edit_form_add", $news_data);

    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "news_form", $news_data));
}

function news_check_edit_authorized(& $news_data) {
    global $config, $sm, $acl_auth;

    if ((!$user = $sm->getSessionUser())) {
        return news_error_msg("L_E_NOACCESS");
    } else {
        $news_data['tos_checked'] = 1;
    }
    if ((defined('ACL') && $acl_auth->acl_ask("admin_all||news_admin")) || (!defined('ACL') && $user['isAdmin'])) {
        $news_data['news_auth'] = "admin";
        return $news_data;
    }
    if ((($news_data['author'] == $user['username']) && $config['NEWS_AUTHOR_CAN_EDIT'])) {
        $news_data['news_auth'] = "author";
        return $news_data;
    }
    if ((($news_data['translator'] == $user['username']) && $config['NEWS_TRANSLATOR_CAN_EDIT'])) {
        $news_data['news_auth'] = "translator";
        return $news_data;
    }

    return news_error_msg("L_E_NOACCESS");
}

function news_form_edit_process() {
    global $LANGDATA, $config;

    $news_data = news_form_getPost();

    if (empty($news_data['nid']) || empty($news_data['lang_id']) || empty($news_data['page'])) {
        return news_error_msg("L_NEWS_NOT_EXIST");
    }
    if (!($news_orig = get_news_byId($news_data['nid'], $news_data['lang_id'], $news_data['page']))) {
        return false; // error already setting in get_news
    }
    if (!news_check_edit_authorized($news_orig)) {
        return false; // error already setting in news_check....
    }

    if (news_form_common_field_check($news_data) == false) {
        return false;
    }
    if ($news_orig['news_auth'] == "admin" || $news_orig['news_auth'] == "author") {
        if (news_form_extra_check($news_data) == false) {
            return false;
        }
    }
    //UPDATE or translate
    if ($news_orig['news_auth'] == "admin" || $news_orig['news_auth'] == "author") {
        if (news_full_update($news_data)) {
            die('[{"status": "ok", "msg": "' . $LANGDATA['L_NEWS_UPDATE_SUCESSFUL'] . '", "url": "' . $config['WEB_URL'] . '"}]');
        } else {
            die('[{"status": "1", "msg": "' . $LANGDATA['L_NEWS_INTERNAL_ERROR'] . '"}]');
        }
    } else if ($news_orig['news_auth'] == "translator") {
        if (news_limited_update($news_data)) {
            die('[{"status": "ok", "msg": "' . $LANGDATA['L_NEWS_UPDATE_SUCESSFUL'] . '", "url": "' . $config['WEB_URL'] . '"}]');
        } else {
            die('[{"status": "1", "msg": "' . $LANGDATA['L_NEWS_INTERNAL_ERROR'] . '"}]');
        }
    }

    return true;
}

function news_full_update($news_data) {
    global $config, $db, $ml;

    if (defined('MULTILANG')) {
        $lang_id = $ml->iso_to_id($news_data['lang']);
    } else {
        $lang_id = $config['WEB_LANG_ID'];
    }

    $query = $db->select_all("news", array("nid" => "{$news_data['nid']}", "lang_id" => "{$news_data['lang_id']}"));
    if (($num_pages = $db->num_rows($query)) <= 0) {
        return false;
    }
    !empty($news_data['acl']) ? $acl = $news_data['acl'] : $acl = "";
    empty($news_data['featured']) ? $news_data['featured'] = 0 : false; //news_clean_featured($lang_id) ;
    !isset($news_data['news_translator']) ? $news_data['news_translator'] = "" : false;


    $set_ary = array(
        "lang_id" => $lang_id, "title" => $news_data['title'], "lead" => $news_data['lead'], "text" => $news_data['text'],
        "featured" => $news_data['featured'], "author" => $news_data['author'], "author_id" => $news_data['author_id'], "category" => $news_data['category'],
        "lang" => $news_data['lang'], "acl" => $acl, "translator" => $news_data['news_translator']
    );

    do_action("news_fulledit_mod_set", $set_ary);

    $where_ary = array(
        "nid" => "{$news_data['nid']}", "lang_id" => "{$news_data['lang_id']}", "page" => "{$news_data['page']}"
    );
    $db->update("news", $set_ary, $where_ary);
    //UPDATE ACL/CATEGORY/LANG/FEATURE on pages;
    if ($num_pages > 1) {
        $page_set_ary = array(
            "featured" => $news_data['featured'], "author" => $news_data['author'], "author_id" => $news_data['author_id'],
            "category" => $news_data['category'], "lang" => $news_data['lang']
        );
        $page_where_ary = array(
            "nid" => "{$news_data['nid']}", "lang_id" => "{$news_data['lang_id']}", "page" => array("operator" => "!=", "value" => "{$news_data['page']}")
        );
        $db->update("news", $page_set_ary, $page_where_ary);
    }

    do_action("news_form_update", $news_data); //MOD

    //SOURCE LINK
    if (!empty($news_data['news_source'])) {
        $source_id = $news_data['nid'];
        $plugin = "Newspage";
        $type = "source";

        $query = $db->select_all("links", array("source_id" => $source_id, "type" => $type, "plugin" => $plugin), "LIMIT 1");
        if ($db->num_rows($query) > 0) {
            $db->update("links", array("link" => $news_data['news_source']), array("source_id" => $source_id, "type" => $type, "plugin" => $plugin));
        } else {
            $insert_ary = array(
                "source_id" => $source_id, "plugin" => $plugin,
                "type" => $type, "link" => $news_data['news_source'],
            );
            $db->insert("links", $insert_ary);
        }
    } else {
        $source_id = $news_data['nid'];
        $plugin = "Newspage";
        $type = "source";
        $db->delete("links", array("source_id" => $source_id, "type" => $type, "plugin" => $plugin), "LIMIT 1");
    }
    //NEW RELATED
    if (!empty($news_data['news_new_related'])) {
        $source_id = $news_data['nid'];
        $plugin = "Newspage";
        $type = "related";
        $insert_ary = array(
            "source_id" => $source_id, "plugin" => $plugin,
            "type" => $type, "link" => $news_data['news_new_related'],
        );
        $db->insert("links", $insert_ary);
    }
    //OLD RELATED
    if (!empty($news_data['news_related'])) {
        foreach ($news_data['news_related'] as $link_id => $value) {
            if (S_VAR_INTEGER($link_id)) { //value its checked on post $link_id no 
                if (empty($value)) {
                    $db->delete("links", array("link_id" => $link_id), "LIMIT 1");
                } else {
                    $db->update("links", array("link" => $value), array("link_id" => $link_id), "LIMIT 1");
                }
            }
        }
    }
    return true;
}

function news_limited_update($news_data) {
    global $config, $db, $ml;

    if (defined('MULTILANG')) {
        $lang_id = $ml->iso_to_id($news_data['lang']);
    } else {
        $lang_id = $config['WEB_LANG_ID'];
    }

    $query = $db->select_all("news", array("nid" => "{$news_data['nid']}", "lang_id" => "{$news_data['lang_id']}"));
    if (($num_pages = $db->num_rows($query)) <= 0) {
        return false;
    }

    $set_ary = array(
        "lang_id" => $lang_id, "title" => $news_data['title'], "lead" => $news_data['lead'], "text" => $news_data['text'],
        "lang" => $news_data['lang']
    );
    do_action("news_limitededit_mod_set", $set_ary);
    $where_ary = array(
        "nid" => "{$news_data['nid']}", "lang_id" => "{$news_data['lang_id']}", "page" => "{$news_data['page']}"
    );
    $db->update("news", $set_ary, $where_ary);

    return true;
}
