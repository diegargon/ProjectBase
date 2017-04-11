<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function Newspage_AdminCategories() {
    global $config, $tpl, $LANGDATA, $ml, $db;

    $catdata['catrow_new'] = "";
    $catdata['catlist'] = "";

    if (defined('MULTILANG')) {
        $langs = $ml->get_site_langs();
    } else {
        $langs['lang_id'] = $config['WEB_LANG_ID'];
        $langs['lang_name'] = $config['WEB_LANG'];
    }

    foreach ($langs as $lang) {
        $catdata['catrow_new'] .= "<label>{$lang['lang_name']}</label> <input type='text' name='{$lang['lang_id']}' value='' />";
    }

    $query = $db->select_all("categories", array("plugin" => "Newspage"), "ORDER BY father, cid");

    $cats = $catsids = [];

    while ($cats_row = $db->fetch($query)) {
        $cats[] = $cats_row;
        $catsids[] = $cats_row['cid'];
    }
    $catsids = array_unique($catsids);
    $foundit = 0;
    foreach ($catsids as $catid) {
        $catdata['catlist'] .= "<form id='cat_mod' method='post' action=''>";
        $catdata['catlist'] .= "<div>";
        $catdata['catlist'] .= "<label>Id</label>";
        $catdata['catlist'] .= "<input type='text' disabled name='cid' class='news_adm_id' value='$catid' />";

        foreach ($langs as $lang) {
            foreach ($cats as $cat) {
                if (($catid == $cat['cid']) && ($cat['lang_id'] == $lang['lang_id'])) {
                    $catdata['catlist'] .= "<label>{$lang['lang_name']}</label>";
                    $catdata['catlist'] .= "<input type='text' name='{$lang['lang_id']}' class='news_adm_name' value='{$cat['name']}' />";
                    $foundit = 1;
                    $catFather = $cat['father'];
                    $catWeight = $cat['weight'];
                }
            }
            if ($foundit == 0) {
                $catdata['catlist'] .= "<label>{$lang['lang_name']}</label> <input type='text' name='{$lang['lang_id']}' value='' />";
            }
            $foundit = 0;
        }

        $catdata['catlist'] .= "<label>{$LANGDATA['L_NEWS_FATHER']}</label>";
        $catdata['catlist'] .= "<input class='news_adm_father' type='text' maxlength='3' name='father' value='$catFather' />";
        $catdata['catlist'] .= "<label>{$LANGDATA['L_NEWS_ORDER']}</label>";
        $catdata['catlist'] .= "<input class='news_adm_order' type='text' maxlength='3' name='weight' value='$catWeight' />";
        $catdata['catlist'] .= "<input type='submit' name='ModCatSubmit' value='{$LANGDATA['L_NEWS_MODIFY']}' />";
        $catdata['catlist'] .= "</div></form>";
    }
    
    return $tpl->getTPL_file("Newspage", "news_adm_cat", $catdata);
}

function Newspage_ModCategories() {
    global $config, $ml, $db;

    if (defined('MULTILANG')) {
        $langs = $ml->get_site_langs();
    } else {
        $langs['lang_id'] = $config['WEB_LANG_ID'];
    }

    foreach ($langs as $lang) {
        $lang_id = $lang['lang_id'];
        $posted_name = S_POST_CHAR_MIDDLE_UNDERSCORE_UNICODE("$lang_id"); // field name value its 1 or 2 depend of lang_id, we get GET['1']
        if (!empty($posted_name)) {
            $posted_cid = S_POST_INT("cid", 11, 1);
            $posted_father = S_POST_INT("father", 3, 1);
            $posted_weight = S_POST_INT("weight", 3, 1);
            if ($posted_cid != false) {
                empty($posted_father) ? $posted_father = 0 : null;
                empty($posted_weight) ? $posted_weight = 0 : null;
                $query = $db->select_all("categories", array("plugin" => "Newspage", "cid" => "$posted_cid", "lang_id" => "$lang_id"));
                if ($db->num_rows($query) > 0) {
                    $db->update("categories", array("name" => "$posted_name", "father" => "$posted_father", "weight" => "$posted_weight"), array("cid" => "$posted_cid", "lang_id" => "$lang_id"));
                } else {
                    $db->insert("categories", array("cid" => "$posted_cid", "father" => "$posted_father", "weight" => "$posted_weight", "lang_id" => "$lang_id", "plugin" => "Newspage", "name" => "$posted_name"));
                }
            }
        }
    }
}

function Newspage_NewCategory() {
    global $config, $ml, $db;

    $new_cid = $db->get_next_num("categories", "cid");

    if (defined('MULTILANG')) {
        $langs = $ml->get_site_langs();
    } else {
        $langs['lang_id'] = $config['WEB_LANG_ID'];
    }

    foreach ($langs as $lang) {
        $lang_id = $lang['lang_id'];
        $posted_name = S_POST_CHARNUM_MIDDLE_UNDERSCORE_UNICODE("$lang_id"); //POST['1'] 2... id return text value
        $posted_father = S_POST_INT("father", 3, 1);
        $posted_weight = S_POST_INT("weight", 3, 1);
        if (!empty($posted_name)) {
            $new_cat_ary = array(
                "cid" => "$new_cid",
                "lang_id" => "{$lang['lang_id']}",
                "plugin" => "Newspage",
                "name" => "$posted_name",
                "father" => "$posted_father",
                "weight" => "$posted_weight"
            );
            $db->insert("categories", $new_cat_ary);
        }
    }
}

function Newspage_AdminModeration() {
    global $config, $LANGDATA, $db;

    $content = "<div>";
    $query = $db->select_all("news", array("moderation" => "1"), "LIMIT {$config['NEWS_NUM_LIST_MOD']}");

    if ($db->num_rows($query) <= 0) {
        return false;
    }
    while ($news_row = $db->fetch($query)) {
        $content .= "<p>"
                . "[<a href='/{$config['CON_FILE']}?module=Newspage&page=news&nid={$news_row['nid']}&lang={$news_row['lang']}&news_delete=1&npage={$news_row['page']}&admin=1'>{$LANGDATA['L_NEWS_DELETE']}</a>]"
                . "[<a href='/{$config['CON_FILE']}?module=Newspage&page=news&nid={$news_row['nid']}&lang={$news_row['lang']}&news_approved={$news_row['nid']}&lang_id={$news_row['lang_id']}&npage={$news_row['page']}&admin=1'>{$LANGDATA['L_NEWS_APPROVED']}</a>]"
                . "<a href='/{$config['CON_FILE']}?module=Newspage&page=news&nid={$news_row['nid']}&lang={$news_row['lang']}&npage={$news_row['page']}&admin=1' target='_blank'>{$news_row['title']}</a>"
                . "</p>";
    }
    $content .= "</div>";

    return $content;
}

function Newspage_InFrontpage() {
    global $LANGDATA, $db, $config;

    $frontpage = "<h3>{$LANGDATA['L_NEWS_INFRONTPAGE']}</h3>";
    $backpage = "<h3>{$LANGDATA['L_NEWS_BACKPAGE']}</h3>";
    $query = $db->select_all("news", array("moderation" => 0, "disabled" => 0), "ORDER BY date DESC");
    while ($news_row = $db->fetch($query)) {
        if ($news_row['frontpage'] == 1) {
            $frontpage .= "<li><span> [" . format_date($news_row['date']) . "] [{$news_row['lang']}] </span><a href='/{$config['CON_FILE']}?module=Newspage&page=news&lang={$news_row['lang']}&nid={$news_row['nid']}&npage=1'>{$news_row['title']}</a></li>";
        } else {
            $backpage .= "<li><span> [" . format_date($news_row['date']) . "]  [{$news_row['lang']}] </span><a href='/{$config['CON_FILE']}?module=Newspage&page=news&lang={$news_row['lang']}&nid={$news_row['nid']}&npage=1'>{$news_row['title']}</a> </li>";
        }
    }
    $content = "<div>";
    $content .= "<section><ul>$frontpage</ul></section>";
    $content .= "<section><ul>$backpage</ul></section>";
    $content .= "</div>";

    return $content;
}
