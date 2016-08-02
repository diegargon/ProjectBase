<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsVote_init() {
    global $tpl;
    print_debug("NewsVote initiated", "PLUGIN_LOAD");

    $tpl->addto_tplvar("SCRIPTS_TOP", NewsVote_script());
    register_action("news_show_page", "newsvote_addrate");
    register_action("news_page_begin", "newsvote_page_begin");
}

function newsvote_addrate($news){
    global $tpl, $sm, $db;
    
    includePluginFiles("NewsVote");
    
    $stars_ext = "_rate.png";

    $user = $sm->getSessionUser();
    $rate_data['uid'] = $user['uid'];

    $rate_data['rate_style'] = "border:0px solid red;"
            . "padding:0px;margin-left:-7px;"
            . "background-color:transparent;"
    ;

    if ($news['rating_closed'] == 0) {
        $where_ary = array (
            "uid" => $user['uid'],
            "section" => "news_rate",
            "resource_id" => $news['nid'],
            "lang_id"  => $news['lang_id'],
        );
        $query = $db->select_all("rating_track", $where_ary, "LIMIT 1");
        if ($db->num_rows($query) == false ) { 
           $rate_data['rate_style'] .=  "cursor:pointer";
        } else {
           $rate_data['btnExtra'] = "disabled";
        }
    } else {
       $rate_data['btnExtra'] = "disabled";
    }
    $rate_stars = NewsVote_GetStars($news['rating'], $stars_ext);
    $rate_data = array_merge($rate_data, $news, $rate_stars);
    $rate_content  = $tpl->getTpl_file("NewsVote", "rate", $rate_data);
    $tpl->addto_tplvar("ADD_NEWS_INFO_POST_AVATAR", $rate_content);
}

function newsvote_page_begin() {
    global $db, $LANGDATA;
    
    includePluginFiles("NewsVote");
    
    if( ($user_rate = S_POST_INT("rate",1,1)) && $user_rate >= 0) {
        $uid = S_POST_INT("rate_uid", 11, 1);
        $news['nid'] = S_POST_INT("rate_rid", 11, 1);
        $news['lang_id'] = S_POST_INT("rate_lid", 11, 1);

        if (empty($uid) || empty($news['nid'] || empty ($news['lang_id']))) {
            $response[] = array("status" => "1", "msg" => $LANGDATA['L_VOTE_INTERNAL_ERROR']);
        }
        //check if already vote
        if (!isset($response) && NewsVote_check_if_vote($uid, $news)) {
            $response[] = array("status" => "2", "msg" => $LANGDATA['L_VOTE_ALREADY_VOTE']);
        } else if (!isset($response)) {
            $insert_ary = array(
                "uid" => "$uid",
                "section" => "news_rate",
                "resource_id" => "{$news['nid']}",
                "lang_id" => "{$news['lang_id']}",
                "vote_value" => "$user_rate",
            );
            $db->insert("rating_track", $insert_ary);
            $response[] = array("status" => "2", "msg" => $LANGDATA['L_VOTE_SUCCESS']);
        }

        NewsVote_Calc_NewsRating($news); //TODO: LIMIT THIS
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        exit();
    }
}

function NewsVote_script() {
    return '<script type="text/javascript" src="/plugins/NewsVote/js/newsvote.js"></script>';
}