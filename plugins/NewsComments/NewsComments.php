<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsComments_init() {
    print_debug("NewsComments initiated", "PLUGIN_LOAD"); 
    register_action("news_show_page", "News_Comments");
}

function News_Comments($news) {
    global $config, $tpl, $sm, $LANGDATA;
    
    includePluginFiles("NewsComments");

    SC_corefiles();
       
    $nid = $news['nid'];
    $lang_id = $news['lang_id'];
            
    $user = $sm->getSessionUser();
    
    if(!empty($_POST['btnSendNewComment'])) {
        if ($config['NC_ALLOW_NEW_COMMENTS']) {
            if ($user || $config['NC_ALLOW_ANON_COMMENTS']) {
                $comment = S_POST_TEXT_UTF8("news_comment");
                $comment ? SC_AddComment("Newspage", $comment, $nid, $lang_id) : false;
            }
        }
    }
    
    $content = SC_GetComments("Newspage", $nid, $lang_id, $config['NC_MAX_COMMENTS']);
    
    if ($config['NC_ALLOW_NEW_COMMENTS']) {
        if ($user || $config['NC_ALLOW_ANON_COMMENTS']) {
            $content .= SC_NewComment("Newspage", $nid, $lang_id);
        }
    }    
    
    $tpl->addto_tplvar("ADD_TO_NEWSSHOW_BOTTOM", $content);
}
