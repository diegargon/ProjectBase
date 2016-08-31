<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

//DIRECT
function NS_basicSearchbox() {
    global $tpl, $config;

    if($config['FRIENDLY_URL']) {
        $sbox_data['searchUrl'] = "/{$config['WEB_LANG']}/search";
    } else {
        $sbox_data['searchUrl'] = "/{$config['CON_FILE']}?module=NewsSearch&page=search&lang={$config['WEB_LANG']}";
    }         
    
    return $search_box = $tpl->getTPL_file("NewsSearch", "NewsSearchBarbox", $sbox_data);
}

function NS_tag_add_form() {
    global $config, $tpl;
    $config['NS_TAGS_SUPPORT'] ? $tpl->addto_tplvar("NEWS_FORM_BOTTOM_OPTION", NS_tags_option()) : false;
}

function NS_news_mod_insert(& $insert_ary) {
    global $db;

    $tags = $db->escape_strip(S_POST_TEXT_UTF8("news_tags"));
    !empty($tags) ? $insert_ary['tags'] = $tags : false;
}

function NS_news_tag_show_page(& $news_row) {
    global $LANGDATA, $tpl, $config;

    if (!empty($news_row['tags'])) {
        $config['PAGE_KEYWORDS'] = $news_row['tags'];
        $exploted_tags = explode(",", $news_row['tags']);
        $tag_data = "<div class='tags'> <p>". $LANGDATA['L_NS_TAGS'] . ": ";
        foreach ($exploted_tags as $tag) {
            $tag = preg_replace('/\s+/', '', $tag);
            if($config['FRIENDLY_URL']) {
                $tag_data .= "<a href='/{$config['WEB_LANG']}/searchTag/$tag'>$tag</a> ";
            } else {
                $tag_data .= "<a href='/{$config['CON_FILE']}&lang={$config['WEB_LANG']}&searchTag=$tag'>$tag</a> ";
            }            
        }
        $tag_data .= "</p></div>";
        $tpl->addto_tplvar("ADD_TO_NEWSSHOW_BOTTOM", $tag_data);
    } else {
        $config['PAGE_KEYWORDS'] = $news_row['title'];
    }
}

function NS_tags_edit_form_add ($news_data) {
    global $tpl;

    $tpl->addto_tplvar("NEWS_FORM_BOTTOM_OPTION", NS_tags_option($news_data['tags']));
}

function NS_news_edit_set_tag(& $set_ary) {
    global $db;

    $tags = $db->escape_strip(S_POST_TEXT_UTF8("news_tags"));
    !empty($tags) ? $set_ary['tags'] = $tags : false;
}

//IN
function NS_tags_option($tags = null) {
    global $LANGDATA, $config;

    $content = "<label for='news_tags'>{$LANGDATA['L_NS_TAGS']}</label>";
    $content .= "<input  value='$tags' maxlength='{$config['NS_TAGS_SZ_LIMIT']}' id='news_tags' class='news_tags' name='news_tags' type='text' placeholder='{$LANGDATA['L_NS_TAGS_PLACEHOLDER']}' />";
    return $content;
}