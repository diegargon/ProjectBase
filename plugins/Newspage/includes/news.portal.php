<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function news_portal() {    
    global $config, $tpl;
          
    $news_nLayout = news_layout_select();
    $news_layout_tpl = "news_body_style" . $news_nLayout++;
    
    if ($config['LAYOUT_SWITCH']) {                   
        $tpl->addto_tplvar("news_nSwitch", $news_nLayout);
        register_action("nav_element", "news_layout_switcher", 6);
    }
    $tpl_data['FEATURED'] = get_news_featured();
    $tpl_data['COL1_ARTICLES'] = get_news(1,0);
    $tpl_data['COL1_ARTICLES'] .= get_news(2,0);
    $tpl_data['COL2_ARTICLES'] = get_news(2,0);
    $tpl_data['COL3_ARTICLES'] = get_news(1,0,1,0);  
    $tpl_data['COL3_ARTICLES'].= get_news(2,0,1,0);    
    $tpl->addtpl_array($tpl_data);
    
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", $news_layout_tpl));     
}

function news_layout_select() {
    global $config;
    
    if(empty($_POST['news_switch']) || $_POST['news_switch'] > $config['NEWS_BODY_STYLES']) {
        $news_switch = 1;
    } else{
        $news_switch = S_POST_INT("news_switch", 1); 
    }
    return $news_switch;    
}

function news_layout_switcher() { 
    global $tpl;
    
    $data = "<li class='nav_left'><form action='#' method='post'>";
    $data .= "<input type='submit'  value='' class='button_switch' />";
    $data .= "<input type='hidden' value=" . $tpl->gettpl_value("news_nSwitch") ." name='news_switch'/>";
    $data .= "</form></li>";
    return $data;
}

function get_news($category, $limit = null, $headlines = 0, $frontpage = 1) {
    global $config, $db, $tpl, $ml;
        
    $content = "";         
   
    $where_ary['featured'] = array("value" => "1", "operator" => "<>");

    $config['NEWS_SELECTED_FRONTPAGE'] ? $where_ary['frontpage'] = $frontpage : false;    
    $config['NEWS_MODERATION'] == 1 ? $where_ary['moderation'] = 0 : false;
        
    if (defined('MULTILANG') && 'MULTILANG') {
        $site_langs = $ml->get_site_langs();
        
        foreach ($site_langs as $site_lang) {
            if ($site_lang['iso_code'] == $config['WEB_LANG']) {
                $lang_id = $site_lang['lang_id'];                               
                $where_ary['lang_id'] = $lang_id;
                break;
            } 
        }
    } 
    
    if ((!empty($category)) && ($category != 0 )) {        
        $where_ary['category'] = $category;
    }       
    $q_extra = " ORDER BY date DESC";    
    $limit > 0 ? $q_extra .= " LIMIT $limit" : false;
    
    $query = $db->select_all("news", $where_ary, $q_extra);
    if ($db->num_rows($query) <= 0) {
        return false;
    }
       
    if(!empty($category)) {
        if (defined('MULTILANG') && 'MULTILANG') {
            $catname = get_category_name($category, $lang_id);
        } else {
            $catname = get_category_name($category);    
        }
        $content .= "<section><h2>$catname</h2>";        
    }     

    while($row = $db->fetch($query)) {
        if ( ($content_data = fetch_news_data($row)) != false) {
            if ($headlines == 1) { $content_data['headlines'] = 1; }
            $content .= $tpl->getTPL_file("Newspage", "news_preview", $content_data);        
        }
    }
    $content .= "</section>";
    $db->free($query);    
    
    return $content;
}

function get_news_featured() {
    global $config, $db, $tpl, $ml;

    //INFO: news_featured skip moderation bit
    $content = "";        
    $where_ary['featured'] = 1;
    if (defined('MULTILANG') && 'MULTILANG') {
        $site_langs = $ml->get_site_langs();
        foreach ($site_langs as $site_lang) {
            if ($site_lang['iso_code'] == $config['WEB_LANG']) {
                $lang_id = $site_lang['lang_id'];
                $where_ary['lang_id'] = $lang_id;
            } 
        }
    }    
    $query = $db->select_all("news", $where_ary, "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        return false;
    }    

    while($row = $db->fetch($query)) {
        if ( ($content_data = fetch_news_data($row)) != false ) {
            if (defined('MULTILANG') && 'MULTILANG') {
                $content_data['CATEGORY'] = get_category_name($row['category'], $lang_id);       
            } else {
                $content_data['CATEGORY'] = get_category_name($$row['category']);
            }            
            $content .= $tpl->getTPL_file("Newspage", "news_featured", $content_data);
        }
    }    
    $db->free($query);
    
    return $content;
}

function fetch_news_data($row) {
    global $config, $acl_auth, $db;    

    
    if( $config['NEWS_ACL_PREVIEW_CHECK']  && defined('ACL') && 'ACL' && 
            !empty($acl_auth) && !empty($row['acl']) && !$acl_auth->acl_ask($row['acl'])) {
        return false;
    }
    
    $data['NID'] = $row['nid'];
    $data['TITLE'] = $row['title'];
    $data['LEAD'] = $row['lead'];                
    $data['date'] = format_date($row['date']);    
    $data['ALT_TITLE'] = htmlspecialchars($row['title']);            

    if ($config['FRIENDLY_URL']) {   
        //FIX: better way for clean all those character?
        $friendly_filter = array('"','\'','?','$',',','.','‘','’',':',';','[',']','{','}','*','!','¡','¿','+','<','>','#','@','|','~','%','&','(',')','=','`','´','/','º','ª','\\');
        $friendly_url = str_replace(' ', "-", $row['title']);
        $friendly_url = str_replace($friendly_filter, "", $friendly_url);
        $data['URL'] = "/".$config['WEB_LANG']."/news/{$row['nid']}/$friendly_url";  
    } else {            
        $data['URL'] = $config['WEB_LANG']. "/newspage.php?nid={$row['nid']}&title=" . str_replace(' ', "_", $row['title']);
    }
    $query = $db->select_all("links", array("source_id" => "{$row['nid']}", "plugin" => "Newspage", "itsmain" => "1"), "LIMIT 1");
    if ($db->num_rows($query) >= 0) {
        $media_row = $db->fetch($query);
        $data['MEDIA'] = news_format_media($media_row);
    }
    $db->free($query);

    return $data;
}

function get_category_name($cid, $lang_id = null) {
    global $db; 
    
    $where_ary['cid'] = $cid;
    if (defined('MULTILANG') && 'MULTILANG' && $lang_id != null) {
        $where_ary['lang_id'] = $lang_id;
    }
    $query = $db->select_all("categories", $where_ary, "LIMIT 1");
    $category = $db->fetch($query);
    $db->free($query);  

    return $category['name'];
}