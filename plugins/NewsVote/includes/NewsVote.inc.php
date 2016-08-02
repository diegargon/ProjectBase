<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsVote_check_if_vote($uid, $news) {
    global $db;
    $where_ary = array (
        "uid" => $uid,
        "section" => "news_rate",
        "resource_id" => $news['nid'],
        "lang_id"  => $news['lang_id'],
    );
    $query = $db->select_all("rating_track", $where_ary, "LIMIT 1");

    return ($db->num_rows($query) == false ) ? false : true;
}

function NewsVote_Calc_NewsRating($news) {
    global $db;
    $where_ary = array (
        "section" => "news_rate",
        "resource_id" => $news['nid'],
        "lang_id" => $news['lang_id'],
    );
    $query = $db->select_all("rating_track", $where_ary);
    $vote_sum = 0;
    if ( ($num_votes = $db->num_rows($query)) > 0) {
        while($vote_row = $db->fetch($query)) {
            $vote_sum = $vote_sum + $vote_row['vote_value'];
        }
        $new_rate = $vote_sum / $num_votes;
        $db->update("news", array("rating" => "$new_rate"), array("nid" => "{$news['nid']}", "lang_id" => "{$news['lang_id']}"));
    }
}

function NewsVote_GetStars($rating, $stars_ext) {
    if ($rating <= 0.25 || empty($rating) ) {
        $rate['stars1'] = $rate['stars2'] = $rate['stars3'] = $rate['stars4'] = $rate['stars5'] = "void" . $stars_ext;
    } else if ($rating <= 0.75) {
        $rate['stars1'] = "half" . $stars_ext;
        $rate['stars2'] = $rate['stars3'] = $rate['stars4'] = $rate['stars5'] = "full" . $stars_ext;
    } else if ($rating <= 1.25) {
        $rate['stars1'] = "full" . $stars_ext;
        $rate['stars2'] = $rate['stars3'] = $rate['stars4'] = $rate['stars5'] = "void" . $stars_ext;
    } else if ($rating <= 1.75) {
        $rate['stars1'] = "full" . $stars_ext;
        $rate['stars2'] = "half" . $stars_ext;
        $rate['stars3'] = $rate['stars4'] = $rate['stars5'] = "void" . $stars_ext;
    } else if ($rating <= 2.25) {
        $rate['stars1'] = $rate['stars2'] = "full" . $stars_ext;
        $rate['stars3'] = $rate['stars4'] = $rate['stars5'] = "void" . $stars_ext;
    } else if ($rating <= 2.75) {
        $rate['stars1'] = $rate['stars2'] = "full" . $stars_ext;
        $rate['stars3'] = "half" . $stars_ext;
        $rate['stars4'] = $rate['stars5'] = "void" . $stars_ext;
    } else if ($rating <= 3.25) {
        $rate['stars1'] = $rate['stars2'] = $rate['stars3'] = "full" . $stars_ext;
        $rate['stars4'] = $rate['stars5'] = "void" . $stars_ext;
    } else if ($rating <= 3.75) {
        $rate['stars1'] = $rate['stars2'] = $rate['stars3'] = "full" . $stars_ext;
        $rate['stars4'] = "half" . $stars_ext;
        $rate['stars5'] = "void" . $stars_ext;
    } else if ($rating <= 4.25) {
        $rate['stars1'] = $rate['stars2'] = $rate['stars3'] = $rate['stars4'] = "full" . $stars_ext;
        $rate['stars5'] = "void" . $stars_ext;
    } else if ($rating <= 4.75) {
        $rate['stars1'] = $rate['stars2'] = $rate['stars3'] = $rate['stars4'] = "full" . $stars_ext;
        $rate['stars5'] = "half" . $stars_ext;
    } else {
        $rate['stars1'] = $rate['stars2'] = $rate['stars3'] = $rate['stars4'] = $rate['stars5'] = "full" . $stars_ext;
    }
    
    return $rate;
}