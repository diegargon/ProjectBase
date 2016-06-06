<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Newspage_AdminCategories() {
    global $config, $LANGDATA;
                
    addto_tplvar("ADM_CONTENT_H2", $LANGDATA['L_NEWS_CATEGORIES']);
    addto_tplvar("ADM_CONTENT", $LANGDATA['L_NEWS_CATEGORY_DESC']);
    
    //MOD CAT
    $content = "<div>";
    $content .= "<p>{$LANGDATA['L_NEWS_MODIFIED_CATS']}</p><br/>";
    $q = "SELECT * FROM {$config['DB_PREFIX']}categories WHERE plugin = 'Newspage'   GROUP BY cid";  
    $query = db_query($q);
    $langs = ML_get_site_langs();
    
    while ($cat_grouped = db_fetch($query)) {
        $content .= "<form id='cat_mod' method='post' action=''>";
        $content .= "<div>";

        foreach ($langs as $lang) {            
            if ($lang['lang_id'] == $cat_grouped['lang_id']) {                
                $content .= "<label>{$lang['lang_name']}</label> <input type='text' name='{$lang['lang_id']}' value='{$cat_grouped['name']}' />";
            } else {                   
                $q2 = "SELECT * FROM {$config['DB_PREFIX']}categories WHERE plugin = 'Newspage'  AND cid = '{$cat_grouped['cid']}' AND lang_id = '{$lang['lang_id']}'";
                $query2 = db_query($q2);
                if(db_num_rows($query2) <= 0) {
                    $content .= "<label>{$lang['lang_name']}</label> <input type='text' name='{$lang['lang_id']}' value='' />";                    
                } else {
                    $other_lang_cat = db_fetch($query2);
                    $content .= "<label>{$lang['lang_name']}</label> <input type='text' name='{$lang['lang_id']}' value='{$other_lang_cat['name']}' />";
                }
            }
        }
        $content .= "<input type='hidden' name='cid' value='{$cat_grouped['cid']}' />" ;                   
        $content .= "<input type='submit' name='ModCatSubmit' value='{$LANGDATA['L_NEWS_MODIFY']}' />" ;   
        $content .= "</div></form><br/>";
    }
            
    $content .= "</div>";
    
    //NEW CAT
    $content .= "<div>";
    
    $content .= "<p>{$LANGDATA['L_NEWS_CREATE_CAT']}</p><br/>";
    $content .= "<form id='cat_new' method='post' action=''>";
    $content .= "<div>";
    $langs = ML_get_site_langs();
    foreach ($langs as $lang) {
        $content .= "<label>{$lang['lang_name']}</label> <input type='text' name='{$lang['lang_id']}' value='' />";        
    }
    $content .= "<input type='submit' name='NewCatSubmit' value='{$LANGDATA['L_NEWS_CREATE']}' />" ;
    $content .= "</div>";        
    $content .= "</form>";

    $content .= "</div>";
    
    return $content;
}

function Newspage_ModCategories() {
   global $config;

   $langs = ML_get_site_langs();
   foreach ($langs as $lang) {
        $lang_id = $lang['lang_id'];
        $posted_name = S_POST_CHAR_UTF8("$lang_id"); // field name value its 1 or 2 depend of lang
        
        if(!empty($posted_name)) {
            $posted_cid = S_POST_INT("cid");
            
            if ($posted_cid != false) {
                $q2 = "SELECT * FROM {$config['DB_PREFIX']}categories WHERE cid = '$posted_cid' AND lang_id = '$lang_id'";
                $query2 = db_query($q2);
                if (db_num_rows($query2) > 0) {
                    $update = "UPDATE {$config['DB_PREFIX']}categories SET name = '$posted_name' WHERE cid = '$posted_cid' AND lang_id = '$lang_id' ";
                    db_query($update);
                } else {
                    $insert = "INSERT INTO {$config['DB_PREFIX']}categories (cid, lang_id, plugin, name ) VALUES ('$posted_cid', '$lang_id', 'Newspage', '$posted_name' );";
                    db_query($insert);
                }
            }
        }
    }
}

function Newspage_NewCategory() {
    global $config;
   
    $plugin = "Newspage";
    $new_cid = db_get_next_num("cid", $config['DB_PREFIX']."categories");
     
    $langs = ML_get_site_langs();
    foreach ($langs as $lang) {    
        $lang_id = $lang['lang_id'];
        $posted_name = S_POST_CHAR_UTF8("$lang_id");
        if (!empty($posted_name))  {
            $insert = "INSERT INTO {$config['DB_PREFIX']}categories (cid, lang_id, plugin, name) VALUES ('$new_cid', '{$lang['lang_id']}', '$plugin', '$posted_name' );";
            db_query($insert);
        } 
    }
}

function Newspage_AdminModeration() {
    global $config, $LANGDATA;

    addto_tplvar("ADM_CONTENT_H2", $LANGDATA['L_NEWS_MODERATION']);
    addto_tplvar("ADM_CONTENT", $LANGDATA['L_NEWS_MODERATION_DESC']);
    
    $content = "<div>";
    $q = "SELECT * FROM {$config['DB_PREFIX']}news WHERE moderation = '1' LIMIT {$config['NEWS_NUM_LIST_MOD']}";
    $query = db_query($q);

    if (db_num_rows($query) > 0) {
        while ($news_row = db_fetch($query)) {
            $content .= "<p>"                    
                    . "[<a href='/newspage.php?nid={$news_row['nid']}&lang={$news_row['lang']}&news_delete={$news_row['nid']}&lang_id={$news_row['lang_id']}&admin=1'>{$LANGDATA['L_NEWS_DELETE']}</a>]"
                    . "[<a href='/newspage.php?nid={$news_row['nid']}&lang={$news_row['lang']}&news_approved={$news_row['nid']}&lang_id={$news_row['lang_id']}&admin=1'>{$LANGDATA['L_NEWS_APPROVED']}</a>]"                        
                    . "<a href='/newspage.php?nid={$news_row['nid']}&admin=1&newslang={$news_row['lang']}' target='_blank'>{$news_row['title']}</a>"
                    . "</p>";
        }
    }
    $content .= "</div>";
    
    return $content;
}