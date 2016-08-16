<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function news_new_page() {
    global $db, $acl_auth, $sm, $tpl, $LANGDATA;

    $nid = S_GET_INT("nid", 11, 1);
    $lang = S_GET_CHAR_AZ("lang", 2, 2);

    $query = $db->select_all("news", array("nid" => $nid, "lang" => $lang), "ORDER BY page");

    if( ($num_pages = $db->num_rows($query)) <= 0) {
        return false;
    }
    $news_first_page = $db->fetch($query);

    $user = $sm->getSessionUser();

    if (!empty($user) && $user['uid'] > 0) {
        $form_data['tos_checked'] = 1;
    }
    if ( ( $news_first_page['author_id'] == $user['uid'])
            || (defined('ACL') && $acl_auth->acl_ask("news_admin||admin_all"))
                    || (!defined('ACL') && $user['isAdmin'])
            ) {
        //Do nothing
    } else {
        return false;
    }
    $form_data['news_auth'] = ''; //not need extra;
    $form_data['NEWS_FORM_TITLE'] = $LANGDATA['L_NEWS_CREATE_NEW_PAGE'];
    $form_data['can_change_author'] = "disabled";
    $form_data['author'] = $user['username'];
    do_action("news_newpage_form_add");
    news_editor_getBar();
    
    $tpl->addto_tplvar("NEWS_FORM_BOTTOM_OTHER_OPTION", 
              "<input type='hidden' name='nid' value='$nid'/>"
            . "<input type='hidden' name='news_lang' value='$lang'/>"
            . "<input type='hidden' name='num_pages' value='$num_pages'/>"
            );

    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "news_form", $form_data));    
}

function news_newpage_form_process() {
    global $LANGDATA, $config;

    $news_data = news_form_getPost();

    if($news_data['title'] == false) {
        $response[] = array("status" => "3", "msg" => $LANGDATA['L_NEWS_TITLE_ERROR']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }
    if( (strlen($news_data['title']) > $config['NEWS_TITLE_MAX_LENGHT']) ||
            (strlen($news_data['title']) < $config['NEWS_TITLE_MIN_LENGHT'])
            ){
        $response[] = array("status" => "3", "msg" => $LANGDATA['L_NEWS_TITLE_MINMAX_ERROR']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }

    if(!empty($news_data['lead'])) {
        if( (strlen($news_data['lead']) > $config['NEWS_LEAD_MAX_LENGHT'])){
            $response[] = array("status" => "4", "msg" => $LANGDATA['L_NEWS_LEAD_MINMAX_ERROR']);
            echo json_encode($response, JSON_UNESCAPED_SLASHES);
            return false;
        }
    }

    if($news_data['text'] == false) {
        $response[] = array("status" => "5", "msg" => $LANGDATA['L_NEWS_TEXT_ERROR']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }
    if( (strlen($news_data['text']) > $config['NEWS_TEXT_MAX_LENGHT']) ||
            (strlen($news_data['text']) < $config['NEWS_TEXT_MIN_LENGHT'])
            ){
        $response[] = array("status" => "5", "msg" => $LANGDATA['L_NEWS_TEXT_MINMAX_ERROR']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }

    $news_data['nid'] = S_POST_INT("nid", 11, 1);
    $news_data['num_pages'] = S_POST_INT("num_pages", 3, 1);

    if (empty($news_data['lang']) || empty($news_data['nid']) || empty($news_data['num_pages'])) {
        $response[] = array("status" => "8", "msg" => $LANGDATA['L_NEWS_INTERNAL_ERROR']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }

    if(news_newpage_submit_new($news_data)) {
        $response[] = array("status" => "ok", "msg" => $LANGDATA['L_NEWS_UPDATE_SUCESSFUL'], "url" => $config['WEB_URL']);
    } else {
        $response[] = array("status" => "8", "msg" => $LANGDATA['L_NEWS_INTERNAL_ERROR']); 
    }
    echo json_encode($response, JSON_UNESCAPED_SLASHES);

    return true;
}


function news_newpage_submit_new($news_data) {
    global $db, $ml, $config;

    if(defined('MULTILANG')) {
        $news_data['lang_id'] = $ml->iso_to_id($news_data['lang']);
    } else {
        $news_data['lang_id'] = $config['WEB_LANG_ID'];
    }

    $query = $db->select_all("news", array("nid" => $news_data['nid'], "lang_id" => $news_data['lang_id'], "page" => 1), "LIMIT 1");
    if( ($db->num_rows($query)) <= 0) {
        return false;
    }
    
    $news_father = $db->fetch($query);

    $insert_ary = array (
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
        "moderation" => $news_father['moderation'],
        "translator" => $news_father['translator'],
        "page" => ++$news_data['num_pages']
    );
    !empty($news_data['lead']) ? $insert_ary['lead'] = $news_data['lead'] : false;

    $db->insert("news", $insert_ary);
    return true;
}