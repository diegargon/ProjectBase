<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Newspage_AdminCategories() {
    global $config, $LANGDATA, $ml, $db;
                
    addto_tplvar("ADM_CONTENT_H2", $LANGDATA['L_NEWS_CATEGORIES']);
    addto_tplvar("ADM_CONTENT", $LANGDATA['L_NEWS_CATEGORY_DESC']);
    
    //MOD CAT
    $content = "<div>";
    $content .= "<p>{$LANGDATA['L_NEWS_MODIFIED_CATS']}</p><br/>";
    //$q = "SELECT * FROM {$config['DB_PREFIX']}categories WHERE plugin = 'Newspage'   GROUP BY cid";  
    $query = $db->select_all("categories", array ("plugin" =>  "Newspage"), "GROUP BY cid");
    //$query = db_query($q);
    if (defined('MULTILANG') && 'MULTILANG') {
        $langs = $ml->get_site_langs();
    } else {
        $langs['lang_id'] = $config['WEB_LANG_ID'];
        $langs['lang_name'] = $config['WEB_LANG'];
    }
    
    //while ($cat_grouped = db_fetch($query)) {
    while ($cat_grouped = $db->fetch($query)) {
        $content .= "<form id='cat_mod' method='post' action=''>";
        $content .= "<div>";

        foreach ($langs as $lang) {            
            if ($lang['lang_id'] == $cat_grouped['lang_id']) {                
                $content .= "<label>{$lang['lang_name']}</label> <input type='text' name='{$lang['lang_id']}' value='{$cat_grouped['name']}' />";
            } else {                   
                //$q2 = "SELECT * FROM {$config['DB_PREFIX']}categories WHERE plugin = 'Newspage'  AND cid = '{$cat_grouped['cid']}' AND lang_id = '{$lang['lang_id']}'";
                $query2 = $db->select_all("categories", array ("plugin" =>  "Newspage", "cid" => "{$cat_grouped['cid']}", "lang_id" => "{$lang['lang_id']}"));
                //$query2 = db_query($q2);
                //if(db_num_rows($query2) <= 0) {
                if($db->num_rows($query2) <= 0) {
                    $content .= "<label>{$lang['lang_name']}</label> <input type='text' name='{$lang['lang_id']}' value='' />";                    
                } else {
                    $other_lang_cat = $db->fetch($query2);//db_fetch($query2);
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

    if (defined('MULTILANG') && 'MULTILANG') {
        $langs = $ml->get_site_langs();
    } else {
        $langs['lang_id'] = $config['WEB_LANG_ID'];
        $langs['lang_name'] = $config['WEB_LANG_NAME'];
    }
     
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
   global $config, $ml, $db;

    if (defined('MULTILANG') && 'MULTILANG') {
        $langs = $ml->get_site_langs();
    } else {
        $langs['lang_id'] = $config['WEB_LANG_ID'];
    }
    
   foreach ($langs as $lang) {
        $lang_id = $lang['lang_id'];
        $posted_name = S_POST_CHAR_UTF8("$lang_id"); // field name value its 1 or 2 depend of lang_id, we get GET['1']
        if(!empty($posted_name)) {
            $posted_cid = S_POST_INT("cid");
            
            if ($posted_cid != false) {
            //    $q2 = "SELECT * FROM {$config['DB_PREFIX']}categories WHERE cid = '$posted_cid' AND lang_id = '$lang_id'";
            //     $query2 = db_query($q2);
                   $query = $db->select_all("categories", array ("plugin" =>  "Newspage", "cid" => "$posted_cid", "lang_id" => "$lang_id"));
                //if (db_num_rows($query2) > 0) {
                if ($db->num_rows($query) > 0) {
                    //$update = "UPDATE {$config['DB_PREFIX']}categories SET name = '$posted_name' WHERE cid = '$posted_cid' AND lang_id = '$lang_id' ";
                    //db_query($update);
                    $db->update("categories", array("name" => "$posted_name"), array("cid" => "$posted_cid", "lang_id" => "$lang_id") );
                } else {
                    $db->insert("categories", array("cid" => "$posted_cid", "lang_id" => "$lang_id", "plugin" => "Newspage", "name" => "$posted_name") );
                    //$insert = "INSERT INTO {$config['DB_PREFIX']}categories (cid, lang_id, plugin, name ) VALUES ('$posted_cid', '$lang_id', 'Newspage', '$posted_name' );";
                    //db_query($insert);
                }
            }
        }
    }
}

function Newspage_NewCategory() {
    global $config, $ml, $db;
   
    $plugin = "Newspage";
    $new_cid = $db->get_next_num("categories", "cid"); //db_get_next_num("cid", $config['DB_PREFIX']."categories");
         
    if (defined('MULTILANG') && 'MULTILANG') {
        $langs = $ml->get_site_langs();           
    } else {
        $langs['lang_id']  = $config['WEB_LANG_ID'];             
    }
    
    foreach ($langs as $lang) {    
        $lang_id = $lang['lang_id'];
        $posted_name = S_POST_CHAR_UTF8("$lang_id"); //POST['1'] 2... id return text value
        if (!empty($posted_name))  {
            $db->insert("categories",  array("cid" => "$new_cid", "lang_id" => "{$lang['lang_id']}", "plugin" => "Newspage", "name" => "$posted_name") );
            //$insert = "INSERT INTO {$config['DB_PREFIX']}categories (cid, lang_id, plugin, name) VALUES ('$new_cid', '{$lang['lang_id']}', '$plugin', '$posted_name' );";
            //db_query($insert);
        } 
    }
}

function Newspage_AdminModeration() {
    global $config, $LANGDATA, $db;

    addto_tplvar("ADM_CONTENT_H2", $LANGDATA['L_NEWS_MODERATION']);
    addto_tplvar("ADM_CONTENT", $LANGDATA['L_NEWS_MODERATION_DESC']);
    
    $content = "<div>";
    //$q = "SELECT * FROM {$config['DB_PREFIX']}news WHERE moderation = '1' LIMIT {$config['NEWS_NUM_LIST_MOD']}";
    //$query = db_query($q);
    $query = $db->select_all("news", array ("moderation" =>  "1"), "LIMIT {$config['NEWS_NUM_LIST_MOD']}");

    //if (db_num_rows($query) > 0) {
    if ($db->num_rows($query) > 0) {
        //while ($news_row = db_fetch($query)) {
        while ($news_row = $db->fetch($query)) {
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

function Newspage_InFrontpage () {
    global $config, $LANGDATA, $db;

    addto_tplvar("ADM_CONTENT_H2", $LANGDATA['L_NEWS_INFRONTPAGE']);
    addto_tplvar("ADM_CONTENT", $LANGDATA['L_NEWS_INFRONTPAGE_DESC']);    
    
    $query = $db->select_all("news", array ("frontpage" =>  1, "moderation" => 0, "disabled" => 0), "ORDER BY date DESC");
    
    $content = "<div><section>";
    $content .= "<h3>{$LANGDATA['L_NEWS_INFRONTPAGE']}</h3>";
    while($news_row = $db->fetch($query)) {
        $content .= "<p><span>[{$news_row['lang']}]  [". format_date($news_row['date'])."] </span><a href='/newspage.php?lang={$news_row['lang']}&nid={$news_row['nid']}'>{$news_row['title']}</a></p>";
    }    
    $content .="</section>";
    $content .="<section>";
    $content .="<h3>{$LANGDATA['L_NEWS_BACKPAGE']}</h3>";
    
    $query = $db->select_all("news", array ("frontpage" => 0, "moderation" => 0, "disabled" => 0), "ORDER BY date DESC");
    while($news_row = $db->fetch($query)) {
        $content .= "<p><span>[{$news_row['lang']}] [". format_date($news_row['date'])."]  </span><a href='/newspage.php?lang={$news_row['lang']}&nid={$news_row['nid']}'>{$news_row['title']}</a></p>";
    }     
    $content .="</section>";
    $content .= "</div>";

    return $content;
}