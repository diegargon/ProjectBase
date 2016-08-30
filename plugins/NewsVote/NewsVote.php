<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsVote_init() {
    print_debug("NewsVote initiated", "PLUGIN_LOAD");
    global $config;

    includePluginFiles("NewsVote");
    if ($config['NEWSVOTE_ON_NEWS'] || $config['NEWSVOTE_ON_NEWS_COMMENTS']) {
        register_action("begin_newsshow", "newsvote_page_begin");
        register_action("begin_newsshow", "newsvote_common_scripts");
    }
    //NEWS    
    $config['NEWSVOTE_ON_NEWS'] ? register_action("news_show_page", "newsvote_news_addrate") : false;
    //NEWS COMMENTS
    $config['NEWSVOTE_ON_NEWS_COMMENTS'] ? register_action("Newspage_get_comments", "newsvote_comment_addrate") : false;
}

function newsvote_common_scripts() {
    global $tpl;
    $tpl->AddScriptFile("standard", "jquery.min", "BOTTOM");
    $tpl->AddScriptFile("NewsVote", "newsvote", "BOTTOM");
}

function newsvote_page_begin() {
    global $config, $sm;

    $user = $sm->getSessionUser();

    if (empty($user)) {
        return false;
    }
    //PROCESS NEWS RATING ACTION
    if (($user_rate = S_POST_INT("news_rate", 1, 1)) && $user_rate >= 0) {
        newsvote_rate_news($user, $user_rate);
    }
    //PROCESS NEWS COMMENT RATE ACTION
    if (($user_rate = S_POST_INT("comment_rate", 1, 1)) && $user_rate >= 0 && $config['NEWSVOTE_ON_NEWS_COMMENTS'] === 1) {
        newsvote_rate_comment($user, $user_rate);
    }
}

function newsvote_rate_news($user, $user_rate) {
    global $db, $config, $LANGDATA;

    $news['nid'] = S_POST_INT("rate_rid", 11, 1);
    $news['lang_id'] = S_POST_INT("rate_lid", 11, 1);

    if (empty($news['nid'] || empty($news['lang_id']))) {
        die('[{"status": "10", "msg": "' . $LANGDATA['L_VOTE_INTERNAL_ERROR'] . '"}]');
    }
    //check if already vote
    if (!NewsVote_check_if_can_vote($user['uid'], $news['nid'], $news['lang_id'], "news_rate")) {
        die('[{"status": "2", "msg": "' . $LANGDATA['L_VOTE_CANT_VOTE'] . '"}]');
    } else {
        $insert_ary = array(
            "uid" => "{$user['uid']}",
            "section" => "news_rate",
            "resource_id" => "{$news['nid']}",
            "lang_id" => "{$news['lang_id']}",
            "vote_value" => "$user_rate",
        );
        $db->insert("rating_track", $insert_ary);
        NewsVote_Calc_Rating($news['nid'], $news['lang_id'], "news_rate"); //TODO: LIMIT USE THIS
        if ($config['NEWSVOTE_NEWS_USER_RATING_N']) {
            newsvote_news_user_rating($news['nid'], $news['lang_id'], $user_rate);
        }
    }
    die('[{"status": "3", "msg": "' . $LANGDATA['L_VOTE_SUCCESS'] . '"}]');
}

function newsvote_rate_comment($user, $user_rate) {
    global $db, $config, $LANGDATA;

    $comment['cid'] = S_POST_INT("rate_rid", 11, 1);
    $comment['lang_id'] = S_POST_INT("rate_lid", 11, 1);

    if (empty($comment['cid'] || empty($comment['lang_id']))) {
        die('[{"status": "4", "msg": "' . $LANGDATA['L_VOTE_INTERNAL_ERROR'] . '"}]');
    }
    //check if already vote
    if (!NewsVote_check_if_can_vote($user['uid'], $comment['cid'], $comment['lang_id'], "news_comments_rate")) {
        die('[{"status": "5", "msg": "' . $LANGDATA['L_VOTE_CANT_VOTE'] . '"}]');
    } else {
        $insert_ary = array(
            "uid" => "{$user['uid']}",
            "section" => "news_comments_rate",
            "resource_id" => "{$comment['cid']}",
            "lang_id" => "{$comment['lang_id']}",
            "vote_value" => "$user_rate",
        );
        $db->insert("rating_track", $insert_ary);
        NewsVote_Calc_Rating($comment['cid'], $comment['lang_id'], "news_comments_rate"); //TODO: LIMIT THIS
        if ($config['NEWSVOTE_COMMENT_USER_RATING']) {
            newsvote_comment_user_rating($comment['cid'], $comment['lang_id'], $user_rate);
        }
    }
    die('[{"status": "6", "msg": "' . $LANGDATA['L_VOTE_SUCCESS'] . '"}]');
}

function newsvote_comment_addrate(& $comment) {
    global $sm, $db, $tpl;

    $stars_ext = "_rate.png";
    $user = $sm->getSessionUser();
    $rate_data['rate_style'] = "border:0px solid red;"
            . "padding:3px;margin-left:-7px;"
            . "background-color:transparent;"
    ;

    if ($user['uid'] > 0 && $user['uid'] != $comment['author_id']) {
        $where_ary = array(
            "uid" => $user['uid'],
            "section" => "news_comments_rate",
            "resource_id" => $comment['cid'],
            "lang_id" => $comment['lang_id'],
        );
        $query = $db->select_all("rating_track", $where_ary, "LIMIT 1");
        ($db->num_rows($query) == false ) ? $rate_data['rate_style'] .= "cursor:pointer" : $rate_data['btnExtra'] = "disabled";
    } else {
        $rate_data['btnExtra'] = "disabled";
    }
    $rate_stars = NewsVote_GetStars($comment['rating'], $stars_ext);
    $rate_data = array_merge($rate_data, $comment, $rate_stars);
    $rate_content = $tpl->getTpl_file("NewsVote", "comment_rate", $rate_data);
    $comment['COMMENT_EXTRA'] = $rate_content;
}

function newsvote_news_addrate($news) {
    global $tpl, $sm, $db;

    $stars_ext = "_rate.png";
    $user = $sm->getSessionUser();
    $rate_data['rate_style'] = "border:0px solid red;"
            . "padding:0px;margin-left:0px;margin-bottom:1px;"
            . "background-color:transparent;"
    ;
    if ($news['rating_closed'] == 0 && $user['uid'] > 0 && $user['uid'] != $news['author_id']) {
        $where_ary = array(
            "uid" => $user['uid'],
            "section" => "news_rate",
            "resource_id" => $news['nid'],
            "lang_id" => $news['lang_id'],
        );
        $query = $db->select_all("rating_track", $where_ary, "LIMIT 1");
        ($db->num_rows($query) == false ) ? $rate_data['rate_style'] .= "cursor:pointer" : $rate_data['btnExtra'] = "disabled";
    } else {
        $rate_data['btnExtra'] = "disabled";
    }
    $rate_stars = NewsVote_GetStars($news['rating'], $stars_ext);
    $rate_data = array_merge($rate_data, $news, $rate_stars);
    $rate_content = $tpl->getTpl_file("NewsVote", "news_rate", $rate_data);
    $tpl->addto_tplvar("ADD_NEWS_INFO_POST_AVATAR", $rate_content);
}

function newsvote_news_user_rating($nid, $lang_id, $user_rating) {
    global $db, $config;

    $query = $db->select_all("news", array("nid" => "$nid", "lang_id" => $lang_id, "page" => 1), "LIMIT 1");
    $news_data = $db->fetch($query);
    $author_xtrData = uXtra_get($news_data['author_id']);
    $new_rating = $author_xtrData['rating_user'] + $user_rating;
    $new_rating_times = ++$author_xtrData['rating_times'];

    uXtra_upsert(array("rating_user" => "$new_rating", "rating_times" => "$new_rating_times"), array("uid" => $author_xtrData['uid']));

    if (!empty($news_data['translator_id']) && $config['NEWSVOTE_NEWS_USER_RATING_NT'] && $news_data['moderation'] == 0) {
        $translator_xtrData = uXtra_get($news_data['translator_id']);
        $t_new_rating = $translator_xtrData['rating_user'] + $user_rating;
        $t_new_rating_times = ++$translator_xtrData['rating_times'];
        uXtra_upsert(array("rating_user" => "$t_new_rating", "rating_times" => "$t_new_rating_times"), array("uid" => $translator_xtrData['uid']));
    }
}

function newsvote_comment_user_rating($cid, $lang_id, $user_rating) {
    global $db, $config;

    $query = $db->select_all("comments", array("cid" => "$cid", "lang_id" => $lang_id), "LIMIT 1");
    $comment_data = $db->fetch($query);
    $author_xtrData = uXtra_get($comment_data['author_id']);
    if ($config['NEWSVOTE_COMMENT_USER_RATING_MODE'] == 1) {
        $new_rating = ++$author_xtrData['rating_user'];
    } else if ($config['NEWSVOTE_COMMENT_USER_RATING_MODE'] == "div2") {
        $new_rating = $author_xtrData['rating_user'] + round($user_rating / 2);
        $new_rating == 0 ? $new_rating = 1 : false;
    } else {
        $new_rating = $author_xtrData['rating_user'] + $user_rating;
    }
    $new_rating_times = ++$author_xtrData['rating_times'];

    uXtra_upsert(array("rating_user" => "$new_rating", "rating_times" => "$new_rating_times"), array("uid" => $author_xtrData['uid']));
}
