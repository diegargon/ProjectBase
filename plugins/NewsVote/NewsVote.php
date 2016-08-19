<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsVote_init() {
    global $tpl;
    print_debug("NewsVote initiated", "PLUGIN_LOAD");

    $tpl->AddScriptFile("standard", "jquery.min", "BOTTOM");
    $tpl->AddScriptFile("NewsVote", "newsvote", "BOTTOM");
    //NEWS
    register_action("news_show_page", "newsvote_addrate");
    register_action("news_page_begin", "newsvote_page_begin");
    //NEWS COMMENTS
    register_action("Newspage_get_comments", "newsvote_comment_addrate");
}

function newsvote_page_begin() {
    global $db, $config, $LANGDATA, $sm;

    includePluginFiles("NewsVote");

    $user = $sm->getSessionUser();
    
    if(empty($user)) {
        return false;
    }
    //PROCESS NEWS RATING ACTION
    if( ($user_rate = S_POST_INT("news_rate",1,1)) && $user_rate >= 0 && $config['NEWSVOTE_ON_NEWS'] === 1) {
        $news['nid'] = S_POST_INT("rate_rid", 11, 1);
        $news['lang_id'] = S_POST_INT("rate_lid", 11, 1);

        if ( empty($news['nid'] || empty ($news['lang_id']))) {
            $response[] = array("status" => "1", "msg" => $LANGDATA['L_VOTE_INTERNAL_ERROR']);          
        }
        //check if already vote
        if (!isset($response) && !NewsVote_check_if_can_vote($user['uid'], $news['nid'], $news['lang_id'], "news_rate")) {
            $response[] = array("status" => "2", "msg" => $LANGDATA['L_VOTE_CANT_VOTE']);
        } else if (!isset($response)) {
            $insert_ary = array(
                "uid" => "{$user['uid']}",
                "section" => "news_rate",
                "resource_id" => "{$news['nid']}",
                "lang_id" => "{$news['lang_id']}",
                "vote_value" => "$user_rate",
            );
            $db->insert("rating_track", $insert_ary);
            NewsVote_Calc_Rating($news['nid'], $news['lang_id'], "news_rate"); //TODO: LIMIT USE THIS
            $response[] = array("status" => "3", "msg" => $LANGDATA['L_VOTE_SUCCESS']);
        }        
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        exit();
    }
    //PROCESS NEWS COMMENT RATE ACTION
    if( ($user_rate = S_POST_INT("comment_rate",1,1)) && $user_rate >= 0 && $config['NEWSVOTE_ON_NEWS_COMMENTS'] === 1) {
        $comment['cid'] = S_POST_INT("rate_rid", 11, 1);
        $comment['lang_id'] = S_POST_INT("rate_lid", 11, 1);

        if ( empty($comment['cid'] || empty ($comment['lang_id']))) {
            $response[] = array("status" => "4", "msg" => $LANGDATA['L_VOTE_INTERNAL_ERROR']);       
        }   
        //check if already vote
        if (!isset($response) && !NewsVote_check_if_can_vote($user['uid'], $comment['cid'], $comment['lang_id'], "news_comments_rate")) {
            $response[] = array("status" => "5", "msg" => $LANGDATA['L_VOTE_CANT_VOTE']);
        } else if (!isset($response)) {
            $insert_ary = array(
                "uid" => "{$user['uid']}",
                "section" => "news_comments_rate",
                "resource_id" => "{$comment['cid']}",
                "lang_id" => "{$comment['lang_id']}",
                "vote_value" => "$user_rate",
            );
            $db->insert("rating_track", $insert_ary);
            NewsVote_Calc_Rating($comment['cid'], $comment['lang_id'], "news_comments_rate"); //TODO: LIMIT THIS
            $response[] = array("status" => "6", "msg" => $LANGDATA['L_VOTE_SUCCESS']);            
        }                
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        exit();
    }
}

function newsvote_comment_addrate(& $comment) {
    global $config, $sm, $db, $tpl;

    includePluginFiles("NewsVote");

    if ($config['NEWSVOTE_ON_NEWS_COMMENTS'] != 1) {
        return false;
    }
    $stars_ext = "_rate.png";
    $user = $sm->getSessionUser();
    $rate_data['rate_style'] = "border:0px solid red;"
            . "padding:3px;margin-left:-7px;"
            . "background-color:transparent;"
    ;

    if ($user['uid'] > 0 && $user['uid'] != $comment['author_id']) {
        $where_ary = array (
            "uid" => $user['uid'],
            "section" => "news_comments_rate",
            "resource_id" => $comment['cid'],
            "lang_id"  => $comment['lang_id'],
        );
        $query = $db->select_all("rating_track", $where_ary, "LIMIT 1");
        ($db->num_rows($query) == false ) ? $rate_data['rate_style'] .=  "cursor:pointer" : $rate_data['btnExtra'] = "disabled";
    } else {
       $rate_data['btnExtra'] = "disabled";
    }
    $rate_stars = NewsVote_GetStars($comment['rating'], $stars_ext);
    $rate_data = array_merge($rate_data, $comment, $rate_stars);
    $rate_content = $tpl->getTpl_file("NewsVote", "comment_rate", $rate_data);
    $comment['COMMENT_EXTRA'] = $rate_content;
}

function newsvote_addrate($news){
    global $tpl, $config, $sm, $db;

    includePluginFiles("NewsVote");

    if ($config['NEWSVOTE_ON_NEWS'] !== 1) {
        return false;
    }
    $stars_ext = "_rate.png";
    $user = $sm->getSessionUser();    
    $rate_data['rate_style'] = "border:0px solid red;"
            . "padding:0px;margin-left:0px;margin-bottom:1px;"
            . "background-color:transparent;"
    ;
    if ($news['rating_closed'] == 0 && $user['uid'] > 0 && $user['uid'] != $news['author_id']) {
        $where_ary = array (
            "uid" => $user['uid'],
            "section" => "news_rate",
            "resource_id" => $news['nid'],
            "lang_id"  => $news['lang_id'],
        );
        $query = $db->select_all("rating_track", $where_ary, "LIMIT 1");
        ($db->num_rows($query) == false ) ? $rate_data['rate_style'] .=  "cursor:pointer" : $rate_data['btnExtra'] = "disabled";
    } else {
       $rate_data['btnExtra'] = "disabled";
    }
    $rate_stars = NewsVote_GetStars($news['rating'], $stars_ext);
    $rate_data = array_merge($rate_data, $news, $rate_stars);
    $rate_content = $tpl->getTpl_file("NewsVote", "news_rate", $rate_data);
    $tpl->addto_tplvar("ADD_NEWS_INFO_POST_AVATAR", $rate_content);
}