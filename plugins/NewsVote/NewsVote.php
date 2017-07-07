<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function NewsVote_init() {
    print_debug("NewsVote initiated", "PLUGIN_LOAD");
    global $cfg;

    includePluginFiles("NewsVote");
    if ($cfg['NEWSVOTE_ON_NEWS'] || $cfg['NEWSVOTE_ON_NEWS_COMMENTS']) {
        register_action("begin_newsshow", "newsvote_page_begin");
        register_action("begin_newsshow", "newsvote_common");
    }
    //NEWS    
    $cfg['NEWSVOTE_ON_NEWS'] ? register_action("news_show_page", "newsvote_news_addrate") : false;
    //NEWS COMMENTS
    $cfg['NEWSVOTE_ON_NEWS_COMMENTS'] ? register_action("Newspage_get_comments", "newsvote_comment_addrate") : false;
}

function newsvote_common() {
    global $tpl;
    $tpl->AddScriptFile("standard", "jquery", "BOTTOM");
    $tpl->AddScriptFile("NewsVote", "newsvote", "BOTTOM");
    $tpl->getCSS_filePath("NewsVote");
}

function newsvote_page_begin() {
    global $cfg, $sm;

    $user = $sm->getSessionUser();

    if (empty($user)) {
        return false;
    }
    //PROCESS NEWS RATING ACTION
    if (($user_rate = S_POST_INT("news_rate", 1, 1)) && $user_rate >= 0) {
        newsvote_rate_news($user, $user_rate);
    }
    //PROCESS NEWS COMMENT RATE ACTION
    if (($user_rate = S_POST_INT("comment_rate", 1, 1)) && $user_rate >= 0 && $cfg['NEWSVOTE_ON_NEWS_COMMENTS'] === 1) {
        newsvote_rate_comment($user, $user_rate);
    }
}

function newsvote_rate_news($user, $user_rate) {
    global $db, $cfg, $LNG;

    $news['nid'] = S_POST_INT("rate_rid", 11, 1);
    $news['lang_id'] = S_POST_INT("rate_lid", 11, 1);

    if (empty($news['nid'] || empty($news['lang_id']))) {
        die('[{"status": "10", "msg": "' . $LNG['L_VOTE_INTERNAL_ERROR'] . '"}]');
    }
    //check if already vote
    if (!NewsVote_check_if_can_vote($user['uid'], $news['nid'], $news['lang_id'], "news_rate")) {
        die('[{"status": "2", "msg": "' . $LNG['L_VOTE_CANT_VOTE'] . '"}]');
    } else {
        $ip = S_SERVER_REMOTE_ADDR();
        $insert_ary = [
            "uid" => "{$user['uid']}",
            "ip" => "$ip",
            "section" => "news_rate",
            "resource_id" => "{$news['nid']}",
            "lang_id" => "{$news['lang_id']}",
            "vote_value" => "$user_rate",
        ];
        $db->insert("rating_track", $insert_ary);
        NewsVote_Calc_Rating($news['nid'], $news['lang_id'], "news_rate"); //TODO: LIMIT USE THIS
        if ($cfg['NEWSVOTE_NEWS_USER_RATING_N']) {
            newsvote_news_user_rating($news['nid'], $news['lang_id'], $user_rate);
        }
    }
    die('[{"status": "3", "msg": "' . $LNG['L_VOTE_SUCCESS'] . '"}]');
}

function newsvote_rate_comment($user, $user_rate) {
    global $db, $cfg, $LNG;

    $comment['cid'] = S_POST_INT("rate_rid", 11, 1);
    $comment['lang_id'] = S_POST_INT("rate_lid", 11, 1);

    if (empty($comment['cid'] || empty($comment['lang_id']))) {
        die('[{"status": "4", "msg": "' . $LNG['L_VOTE_INTERNAL_ERROR'] . '"}]');
    }
    //check if already vote
    if (!NewsVote_check_if_can_vote($user['uid'], $comment['cid'], $comment['lang_id'], "news_comments_rate")) {
        die('[{"status": "5", "msg": "' . $LNG['L_VOTE_CANT_VOTE'] . '"}]');
    } else {
        $ip = S_SERVER_REMOTE_ADDR();
        $insert_ary = [
            "uid" => "{$user['uid']}",
            "ip" => "$ip",
            "section" => "news_comments_rate",
            "resource_id" => "{$comment['cid']}",
            "lang_id" => "{$comment['lang_id']}",
            "vote_value" => "$user_rate",
        ];
        $db->insert("rating_track", $insert_ary);
        NewsVote_Calc_Rating($comment['cid'], $comment['lang_id'], "news_comments_rate"); //TODO: LIMIT THIS
        if ($cfg['NEWSVOTE_COMMENT_USER_RATING']) {
            newsvote_comment_user_rating($comment['cid'], $comment['lang_id'], $user_rate);
        }
    }
    die('[{"status": "6", "msg": "' . $LNG['L_VOTE_SUCCESS'] . '"}]');
}

function newsvote_comment_addrate(& $comment) {
    global $sm, $db, $tpl, $cfg;

    $user = $sm->getSessionUser();

    $rate_data['btnExtra'] = " style=\"background: url({$cfg['NEWSVOTE_STARS_URL']}) no-repeat;\" ";
    if ($user['uid'] > 0 && $user['uid'] != $comment['author_id']) {
        $where_ary = [
            "uid" => $user['uid'],
            "section" => "news_comments_rate",
            "resource_id" => $comment['cid'],
            "lang_id" => $comment['lang_id'],
        ];
        $query = $db->select_all("rating_track", $where_ary, "LIMIT 1");
        ($db->num_rows($query) == false ) ? $rate_data['show_pointer'] = 1 : $rate_data['btnExtra'] .= "disabled";
    } else {
        $rate_data['btnExtra'] .= "disabled";
    }


    $rate_stars = NewsVote_GetStars($comment['rating']);
    $rate_data = array_merge($rate_data, $comment, $rate_stars);
    $rate_content = $tpl->getTpl_file("NewsVote", "comment_rate", $rate_data);
    $comment['COMMENT_EXTRA'] = $rate_content;
}

function newsvote_news_addrate($news) {
    global $tpl, $sm, $db, $cfg;

    $user = $sm->getSessionUser();

    $rate_data['btnExtra'] = " style=\"background: url({$cfg['NEWSVOTE_STARS_URL']}) no-repeat;\" ";
    if ($news['rating_closed'] == 0 && $user['uid'] > 0 && $user['uid'] != $news['author_id']) {
        $where_ary = [
            "uid" => $user['uid'],
            "section" => "news_rate",
            "resource_id" => $news['nid'],
            "lang_id" => $news['lang_id'],
        ];
        $query = $db->select_all("rating_track", $where_ary, "LIMIT 1");
        ($db->num_rows($query) == false ) ? $rate_data['show_pointer'] = 1 : $rate_data['btnExtra'] .= "disabled";
    } else {
        $rate_data['btnExtra'] .= "disabled";
    }
    $rate_stars = NewsVote_GetStars($news['rating']);
    $rate_data = array_merge($rate_data, $news, $rate_stars);
    $rate_content = $tpl->getTpl_file("NewsVote", "news_rate", $rate_data);
    $tpl->addto_tplvar("ADD_NEWS_INFO_POST_AVATAR", $rate_content);
}

function newsvote_news_user_rating($nid, $lang_id, $user_rating) {
    global $db, $cfg, $UXtra;

    $query = $db->select_all("news", [ "nid" => "$nid", "lang_id" => $lang_id, "page" => 1], "LIMIT 1");
    $news_data = $db->fetch($query);
    $author_xtrData = $UXtra->getById($news_data['author_id']);
    if ($author_xtrData == false) {
        $author_xtrData['uid'] = $news_data['author_id'];
        $author_xtrData['rating_user'] = 0;
        $author_xtrData['rating_times'] = 0;
    }
    $new_rating = $author_xtrData['rating_user'] + $user_rating;
    $new_rating_times = ++$author_xtrData['rating_times'];

    $UXtra->upsert(["rating_user" => "$new_rating", "rating_times" => "$new_rating_times"], ["uid" => $author_xtrData['uid']]);

    if (!empty($news_data['translator_id']) && $cfg['NEWSVOTE_NEWS_USER_RATING_NT'] && $news_data['moderation'] == 0) {
        $translator_xtrData = $UXtra->getById($news_data['translator_id']);
        $t_new_rating = $translator_xtrData['rating_user'] + $user_rating;
        $t_new_rating_times = ++$translator_xtrData['rating_times'];
        $UXtra->upsert(["rating_user" => "$t_new_rating", "rating_times" => "$t_new_rating_times"], ["uid" => $translator_xtrData['uid']]);
    }
}

function newsvote_comment_user_rating($cid, $lang_id, $user_rating) {
    global $db, $cfg, $UXtra;

    $query = $db->select_all("comments", ["cid" => "$cid", "lang_id" => $lang_id], "LIMIT 1");
    $comment_data = $db->fetch($query);
    $author_xtrData = $UXtra->getById($comment_data['author_id']);
    if ($author_xtrData == false) {
        $author_xtrData['uid'] = $comment_data['author_id'];
        $author_xtrData['rating_user'] = 0;
        $author_xtrData['rating_times'] = 0;
    }
    if ($cfg['NEWSVOTE_COMMENT_USER_RATING_MODE'] == 1) {
        $new_rating = ++$author_xtrData['rating_user'];
    } else if ($cfg['NEWSVOTE_COMMENT_USER_RATING_MODE'] == "div2") {
        $new_rating = $author_xtrData['rating_user'] + round($user_rating / 2);
        $new_rating == 0 ? $new_rating = 1 : false;
    } else {
        $new_rating = $author_xtrData['rating_user'] + $user_rating;
    }
    $new_rating_times = ++$author_xtrData['rating_times'];

    $UXtra->upsert(["rating_user" => "$new_rating", "rating_times" => "$new_rating_times"], ["uid" => $author_xtrData['uid']]);
}
