<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsFormMediaTpl($news_data) {
    global $tpl;
    $tpl->addto_tplvar("NEWS_FORM_MIDDLE_OPTION", $tpl->getTPL_file("NewsMedia", "NewsMediaItems", $news_data)); 
}

function NewsEditFormMediaTpl($news_data) {
    global $tpl;

    if ( ($media_ary = get_links($news_data['nid'], "image", array("itsmain" => 1), "LIMIT 1")) != false ) {
        foreach ($media_ary as $media) {
            $news_data['main_media'] = $media['link'];
        }
    }
    $tpl->addto_tplvar("NEWS_FORM_MIDDLE_OPTION", $tpl->getTPL_file("NewsMedia", "NewsMediaItems", $news_data)); 
}

function NewsMediaCheck(&$news_data) {
    global $config, $LANGDATA;

    $error_msg = "";

    if(!empty($_POST['news_main_media'])) {
        $link = S_VALIDATE_MEDIA($_POST['news_main_media'], $config['NEWS_MEDIA_MAX_LENGHT'], $config['NEWS_MEDIA_MIN_LENGHT']);
        ($link == -1) ? $error_msg = $LANGDATA['L_NEWS_MAIN_MEDIALINK_ERROR'].":\n". $_POST['news_main_media'] ."\n" : false;
        (!empty($link) && $link != -1) ? $news_data['news_main_media'] = $link: false;
    }

    return (!empty($error_msg)) ? $error_msg : false;
}

function NewsMediaInsertNew($news_data) {
    global $db;

    $plugin = "Newspage";    
    $type = "image";
    $insert_ary = array (
        "source_id" => $news_data['nid'],
        "plugin" => $plugin,
        "type" => $type,
        "link" => $news_data['news_main_media'],
        "itsmain" => 1
    );

    $db->insert("links", $insert_ary);        
}

function news_form_media_update($news_data) {
    global $db;    
    $plugin = "Newspage";
    $type = "image";

    if(!empty($news_data['news_main_media'])) {
        $query = $db->select_all("links", array("source_id" => $news_data['nid'], "type" => $type, "plugin" => $plugin, "itsmain" => 1 ), "LIMIT 1");
        if ($db->num_rows($query) > 0) {       
            $db->update("links", array("link" => $news_data['news_main_media']), array("source_id" => $news_data['nid'], "type" => $type, "itsmain" => 1));
        } else {
            $insert_ary = array ( 
                "source_id" => $news_data['nid'],
                "plugin" => $plugin,
                "type" => $type,
                "link" => $news_data['news_main_media'],
                "itsmain" => 1
            );            
            $db->insert("links", $insert_ary);
        }        
    }
}

function news_media_featured_mod($news) {
    global $tpl;

    if ( ($media_ary = get_links($news['nid'], "image", array("itsmain" => 1), "LIMIT 1")) != false ) {
        $media_content = news_format_media($media_ary);
        $content = "<div class='feature_image'>$media_content</div>";
        $tpl->addto_tplvar("news_featured_article_pre", $content);
    }    
}

function news_media_getnews_mod ($news) {
    global $tpl;
        
    if ( ($media_ary = get_links($news['NID'], "image", array("itsmain" => 1), "LIMIT 1")) != false ) { //FIX NID to nid
        $media_content = news_format_media($media_ary);
        $tpl->addto_tplvar_uniq("news_preview_lead_pre", $media_content);
    }
}

function news_media_page_mod($news) {
    global $tpl;
        
    if ( ($media_ary = get_links($news['nid'], "image", array("itsmain" => 1), "LIMIT 1")) != false ) {
        $content = "<div class='article_main_media'>". news_format_media($media_ary) ."</div>";
        $tpl->addto_tplvar("news_main_pre_text", $content);
    }
}