<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsComments_init() {
    print_debug("NewsComments initiated", "PLUGIN_LOAD");
    register_action("news_show_page", "News_Comments");
    register_action("Newspage_get_comments", "News_Comment_Details");
}

function News_Comments($news) {
    global $config, $tpl, $sm;

    includePluginFiles("NewsComments");

    $nid = $news['nid'];
    $lang_id = $news['lang_id'];

    $user = $sm->getSessionUser();

    if (empty($nid) || empty($lang_id)) {
        return false;
    }

    if (!empty($_POST['btnSendNewComment']) && $config['NC_ALLOW_NEW_COMMENTS']) {
        if (!empty($user) || $config['NC_ALLOW_ANON_COMMENTS']) {
            $comment = S_POST_TEXT_UTF8("news_comment");
            $comment ? SC_AddComment("Newspage", $comment, $nid, $lang_id) : false;
        }
    }

    $content = SC_GetComments("Newspage", $nid, $lang_id, $config['NC_MAX_COMMENTS_PERPAGE']);

    if ($config['NC_ALLOW_NEW_COMMENTS']) {
        if ($user || $config['NC_ALLOW_ANON_COMMENTS']) {
            $content .= SC_NewComment("Newspage", $nid, $lang_id);
        }
    }
    $tpl->addto_tplvar("ADD_TO_NEWSSHOW_BOTTOM", $content);

    return true;
}

function News_Comment_Details(& $comment) {
    global $sm, $config;

    $author_data = $sm->getUserByID($comment['author_id']);
    if ($config['FRIENDLY_URL']) {
        $comment['p_url'] = "/{$config['WEB_LANG']}/profile&viewprofile={$author_data['uid']}";
    } else {
        $comment['p_url'] = "/{$config['CON_FILE']}?module=SMBasic&page=profile&viewprofile={$author_data['uid']}&lang={$config['WEB_LANG']}";
    }
    $comment = array_merge($comment, $author_data);
}
