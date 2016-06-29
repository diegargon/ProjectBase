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
    if ( ($media = get_news_main_link_byID($news_data['nid'])) != false) {
        $news_data['main_media'] = $media['link'];
    }  
    
    $tpl->addto_tplvar("NEWS_FORM_MIDDLE_OPTION", $tpl->getTPL_file("NewsMedia", "NewsMediaItems", $news_data)); 
}

function NewsMediaCheck() {
    global $config, $LANGDATA;
    
    $error_msg = "";
    
    if(!empty($_POST['news_main_media'])) {
        $link = S_VALIDATE_MEDIA($_POST['news_main_media'], $config['NEWS_MEDIA_MAX_LENGHT'], $config['NEWS_MEDIA_MIN_LENGHT']); 
        ($link == -1) ? $error_msg = $LANGDATA['L_NEWS_MAIN_MEDIALINK_ERROR'].":\n". $_POST['news_main_media'] ."\n" : false;
    } 
    
    return (!empty($error_msg)) ? $error_msg : false;
}

function NewsMediaInsertNew($source_id) {
    global $db, $config;
    $news_media = S_VALIDATE_MEDIA($_POST['news_main_media'], $config['NEWS_MEDIA_MAX_LENGHT'], $config['NEWS_MEDIA_MIN_LENGHT'], 1);
    $plugin = "Newspage";
    //TODO DETERMINE IF OTS IMAGE OR VIDEO ATM VALIDATOR ONLY ACCEPT IMAGES, IF ITS NOT A IMAGE WE MUST  CHECK IF ITS A VIDEO OR SOMETHING LIKE THAT
    $type = "image";
    $insert_ary = array (
        "source_id" => $source_id,
        "plugin" => $plugin,
        "type" => $type,
        "link" => $news_media,
        "itsmain" => 1
    );
    
    $db->insert("links", $insert_ary);        
}

function news_form_media_update($source_id) {
    global $db, $config;
    //TODO DETERMINE IF OTS IMAGE OR VIDEO ATM VALIDATOR ONLY ACCEPT IMAGES, IF ITS NOT A IMAGE WE MUST  CHECK IF ITS A VIDEO OR SOMETHING LIKE THAT
    $plugin = "Newspage";
    $type = "image";

    $news_media = S_VALIDATE_MEDIA($_POST['news_main_media'], $config['NEWS_MEDIA_MAX_LENGHT'], $config['NEWS_MEDIA_MIN_LENGHT']);
    
    $query = $db->select_all("links", array("source_id" => $source_id, "type" => $type, "plugin" => $plugin, "itsmain" => 1 ));
    if ($db->num_rows($query) > 0) {       
        $db->update("links", array("link" => $news_media), array("source_id" => $source_id, "type" => $type, "itsmain" => 1));
    } else {
        $insert_ary = array ( 
            "source_id" => $source_id,
            "plugin" => $plugin,
            "type" => $type,
            "link" => $news_media,
            "itsmain" => 1
        );            
        $db->insert("links", $insert_ary);
    }        
}

function news_media_featured_mod($news) {
    global $tpl;
    
    if ( ($media = get_news_main_link_byID($news['nid'])) != false) {
        $media_content = news_format_media($media);
        $content = "<div class='feature_image'>$media_content</div>";
        $tpl->addto_tplvar("news_featured_article_pre", $content);
    }
}

function news_media_getnews_mod ($news) {
    global $tpl;
    
    if ( ($media = get_news_main_link_byID($news['NID'])) != false) { //FIX : NID->nid
        $media_content = news_format_media($media);
        $tpl->addto_tplvar_uniq("news_preview_lead_pre", $media_content);
    }
}

function news_media_page_mod($news) {
    global $tpl;
    
    if ( ($media = get_news_main_link_byID($news['nid'])) != false) {
        $content = "<div class='article_main_media'>". news_format_media($media) ."</div>";
        $tpl->addto_tplvar("news_main_pre_text", $content);
    }    
    
}