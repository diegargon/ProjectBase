<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Newspage_AdminCategories() {
    global $config, $LANGDATA, $ml, $db, $tpl;
                
    $tpl->addto_tplvar("ADM_CONTENT_H2", $LANGDATA['L_NEWS_CATEGORIES']);
    $tpl->addto_tplvar("ADM_CONTENT", $LANGDATA['L_NEWS_CATEGORY_DESC']);
    
    //MOD CAT
    $content = "<div>";
    $content .= "<p>{$LANGDATA['L_NEWS_MODIFIED_CATS']}</p>";
    $query = $db->select_all("categories", array ("plugin" =>  "Newspage"), "GROUP BY cid");
    
    if (defined('MULTILANG') && 'MULTILANG') {
        $langs = $ml->get_site_langs();
    } else {
        $langs['lang_id'] = $config['WEB_LANG_ID'];
        $langs['lang_name'] = $config['WEB_LANG'];
    }
        
    while ($cat_grouped = $db->fetch($query)) {
        $content .= "<form id='cat_mod' method='post' action=''>";
        $content .= "<div>";

        foreach ($langs as $lang) {            
            if ($lang['lang_id'] == $cat_grouped['lang_id']) {                
                $content .= "<label>{$lang['lang_name']}</label> <input type='text' name='{$lang['lang_id']}' value='{$cat_grouped['name']}' />";
            } else {                   
                
                $query2 = $db->select_all("categories", array ("plugin" =>  "Newspage", "cid" => "{$cat_grouped['cid']}", "lang_id" => "{$lang['lang_id']}"));
                if($db->num_rows($query2) <= 0) {
                    $content .= "<label>{$lang['lang_name']}</label> <input type='text' name='{$lang['lang_id']}' value='' />";                    
                } else {
                    $other_lang_cat = $db->fetch($query2);
                    $content .= "<label>{$lang['lang_name']}</label> <input type='text' name='{$lang['lang_id']}' value='{$other_lang_cat['name']}' />";
                }
            }
        }
        $content .= "<input type='hidden' name='cid' value='{$cat_grouped['cid']}' />" ;                   
        $content .= "<input type='submit' name='ModCatSubmit' value='{$LANGDATA['L_NEWS_MODIFY']}' />" ;   
        $content .= "</div></form>";
    }
            
    $content .= "</div>";
    
    //NEW CAT
    $content .= "<div>";
    
    $content .= "<p>{$LANGDATA['L_NEWS_CREATE_CAT']}</p>";
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
                   $query = $db->select_all("categories", array ("plugin" =>  "Newspage", "cid" => "$posted_cid", "lang_id" => "$lang_id"));
                if ($db->num_rows($query) > 0) {
                    $db->update("categories", array("name" => "$posted_name"), array("cid" => "$posted_cid", "lang_id" => "$lang_id") );
                } else {
                    $db->insert("categories", array("cid" => "$posted_cid", "lang_id" => "$lang_id", "plugin" => "Newspage", "name" => "$posted_name") );
                }
            }
        }
    }
}

function Newspage_NewCategory() {
    global $config, $ml, $db;
   
    $new_cid = $db->get_next_num("categories", "cid");
         
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
        } 
    }
}

function Newspage_AdminModeration() {
    global $config, $LANGDATA, $db, $tpl;

    $tpl->addto_tplvar("ADM_CONTENT_H2", $LANGDATA['L_NEWS_MODERATION']);
    $tpl->addto_tplvar("ADM_CONTENT", $LANGDATA['L_NEWS_MODERATION_DESC']);
    
    $content = "<div>";
    $query = $db->select_all("news", array ("moderation" =>  "1"), "LIMIT {$config['NEWS_NUM_LIST_MOD']}");

    if ($db->num_rows($query) > 0) {
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
    global $LANGDATA, $db, $tpl;

    $tpl->addto_tplvar("ADM_CONTENT_H2", $LANGDATA['L_NEWS_INFRONTPAGE']);
    $tpl->addto_tplvar("ADM_CONTENT", $LANGDATA['L_NEWS_INFRONTPAGE_DESC']);    

    $frontpage = "<h3>{$LANGDATA['L_NEWS_INFRONTPAGE']}</h3>";
    $backpage = "<h3>{$LANGDATA['L_NEWS_BACKPAGE']}</h3>";
    
    $query = $db->select_all("news", array ("moderation" => 0, "disabled" => 0), "ORDER BY date DESC");
    while($news_row = $db->fetch($query)) {
        if ($news_row['frontpage'] == 1) {
            $frontpage .= "<li><span> [". format_date($news_row['date'])."] [{$news_row['lang']}] </span><a href='/newspage.php?lang={$news_row['lang']}&nid={$news_row['nid']}'>{$news_row['title']}</a></li>";
        } else {
            $backpage .= "<li><span> [". format_date($news_row['date'])."]  [{$news_row['lang']}] </span><a href='/newspage.php?lang={$news_row['lang']}&nid={$news_row['nid']}'>{$news_row['title']}</a> </li>";
        }
    }
        
    $content = "<div>";
    $content .= "<section><ul>$frontpage</ul></section>";
    $content .= "<section><ul>$backpage</ul></section>";
    $content .= "</div>";
    
    return $content;

}