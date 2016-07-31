<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function news_show_page() {
    global $config, $LANGDATA, $tpl, $sm, $acl_auth;

    if( (empty($_GET['nid'])) || ($nid = S_GET_INT("nid", 8, 1)) == false ||
        (empty($_GET['lang'])) || ($lang = S_GET_CHAR_AZ("lang", 2, 2)) == false) {
        $msgbox['MSG'] = "L_NEWS_NOT_EXIST";
        do_action("message_box", $msgbox);
        return false;
    }

    if($config['NEWS_MULTIPLE_PAGES'] && !empty($_GET['page'])) {
        $page = S_GET_INT("page", 11, 1);  
    } else {
        $page = 1; 
    }

    if ( S_GET_INT("admin") ) {
        if (defined("ACL") && !$acl_auth->acl_ask("admin_all||news_admin") ) {
                $msgbox['MSG'] = "L_ERROR_NOACCESS";
                do_action("message_box", $msgbox); 
                return false;
        }
        if ( !defined('ACL')) {
            $user = $sm->getSessionUser();
            if (empty($user) || $user['isAdmin'] != 1)  {
                $msgbox['MSG'] = "L_ERROR_NOACCESS";
                do_action("message_box", $msgbox);
                return false;
            }
        }
    }
    news_process_admin_actions();

    if( ($news_row = get_news_byId($nid, $lang, $page)) == false) {
        return false;
    }

    if ($config['NEWS_STATS']) {
        news_stats($nid, $lang, $page, $news_row['visits']);
    }
    if( $config['NEWS_MULTIPLE_PAGES']) {
        $tpl->addto_tplvar("ADD_TO_NEWSSHOW_BOTTOM", news_pager($news_row));
    }
    if (!empty($news_row['tags'])) {
        $config['PAGE_KEYWORDS'] = $news_row['tags'];        
        $exploted_tags = explode(",", $news_row['tags']);
        $tag_data = "<p>". $LANGDATA['L_NEWS_TAGS'] . ": ";
        foreach ($exploted_tags as $tag) {
            $tag_data .= "<a href=''>$tag</a> ";
        }
        $tag_data .= "</p>";
        $tpl->addto_tplvar("ADD_TO_NEWSSHOW_BOTTOM", $tag_data);
    } else {
        $config['PAGE_KEYWORDS'] = $news_row['title'];
    }
    
    do_action("news_show_page", $news_row);

    if ($config['NEWS_META_OPENGRAPH']) {
        news_add_social_meta($news_row);
    }
    $tpl->addto_tplvar("NEWS_ADMIN_NAV", news_nav_options($news_row));

    $config['PAGE_DESC'] = $news_row['title'] . ":" . $news_row['lead'];
    
    $tpl_data['nid'] = $news_row['nid'];
    $tpl_data['news_title'] = str_replace('\r\n', '', $news_row['title']);
    $tpl_data['news_lead'] = str_replace('\r\n', PHP_EOL, $news_row['lead']);
    $tpl_data['news_url'] = "news.php?nid={$news_row['nid']}";
    $tpl_data['news_date'] = format_date($news_row['date']);
    $tpl_data['news_author'] = $news_row['author'];
    $tpl_data['news_author_uid'] = $news_row['author_id'];
    //$tpl_data['news_text']  = str_replace('\r\n', PHP_EOL, $news_row['text']);
    
    !isset($news_parser) ? $news_parser = new parse_text : false;
    $tpl_data['news_text']  = $news_parser->parse($news_row['text']);
    if (!empty ($news_row['translator'])) {
        $translator = $sm->getUserByUsername($news_row['translator']);
        $tpl_data['news_translator'] = "<a rel='nofollow' href='/profile.php?lang={$config['WEB_LANG']}&viewprofile={$translator['uid']}'>{$translator['username']}</a>";
    }   
    $author = $sm->getUserByID($news_row['author_id']);
    $config['PAGE_AUTHOR'] = $author['username'];
    $tpl_data['author_avatar'] = "<div class='avatar'><img width='50' src='{$author['avatar']}' alt='' /></div>";
    
    $tpl->addtpl_array($tpl_data);

    if ( ($news_source = get_news_source_byID($news_row['nid'])) != false  && $config['NEWS_SOURCE'] ) {
        $tpl->addto_tplvar("NEWS_SOURCE", news_format_source($news_source));
    }
    if ( $config['NEWS_RELATED'] && ($news_related = news_get_related($news_row['nid'])) != false) {
        $related_content = "<span>{$LANGDATA['L_NEWS_RELATED']}:</span>";
        foreach ($news_related as $related) {
            $related_content .= "<li><a rel='nofollow' target='_blank' href='{$related['link']}'>{$related['link']}</a></li>";
        }
        $tpl->addto_tplvar("NEWS_RELATED", $related_content);
    }
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "news_show_body"));
}

function news_process_admin_actions() {
    global $config, $acl_auth, $sm, $ml;

    //if we enter with &admin=1 already passing the admin check in news_show_page, check if not enter with admin=1
    if (defined("ACL") && !S_GET_INT("admin") ) { 
        if(!$acl_auth->acl_ask("admin_all || news_admin")) { 
           return false;
        }
    } else if (!defined("ACL") && !S_GET_INT("admin")) {
        if ( ($user = $sm->getSessionUser()) == false || $user['isAdmin'] != 1) {
            return false;
        }
    }
    if (!empty($_GET['news_delete']) ) {
        $delete_nid = S_GET_INT("nid", 11, 1); 
        $delete_lang = S_GET_CHAR_AZ("lang", 2, 2);
        if (!empty($delete_nid) && !empty($delete_lang)) {
            defined('MULTILANG') ? $delete_lang_id = $ml->iso_to_id($delete_lang) : $delete_lang_id = $config['WEB_LANG_ID'];
            news_delete($delete_nid, $delete_lang_id);
            S_GET_CHAR_AZ("backlink") == "home" ? header("Location: /{$config['WEB_LANG']}") : header("Location: ". S_SERVER_URL("HTTP_REFERER") ."");
        }
    }
    if (!empty($_GET['news_approved']) && !empty($_GET['lang_id']) &&
        $_GET['news_approved'] > 0 && $_GET['lang_id'] > 0) {
        news_approved(S_GET_INT("news_approved"), S_GET_INT("lang_id"));
    }
    if (isset($_GET['news_featured']) && !empty($_GET['lang_id'] && !empty($_GET['nid']))) {
        empty($_GET['news_featured']) ? $news_featured = 0: $news_featured = 1;
        news_featured(S_GET_INT("nid", 11, 1), $news_featured, S_GET_INT("lang_id"));
    }
    if ( isset($_GET['news_frontpage']) && !empty($_GET['lang_id']) ) {
        news_frontpage( S_GET_INT("nid", 11, 1), S_GET_INT("lang_id"), S_GET_INT("news_frontpage", 1, 1));  
    }

    return true;
}
 
function news_nav_options($news) {
    global $LANGDATA, $config, $sm, $acl_auth;
    $content = "";
    $user = $sm->getSessionUser();
    // EDIT && NEW PAGE: ADMIN, AUTHOR or Translator
    if( (defined('ACL') && $acl_auth->acl_ask("admin_all||news_admin")) 
            || (!defined('ACL') && $user['isAdmin'] == 1) 
            || ($news['author'] == $user['username']) 
            || (!empty($news['translator']) && ($news['translator'] == $user['username']))
    ) {       
        $content .= "<li><a rel='nofollow' href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&page={$news['page']}&newsedit={$news['nid']}&lang_id={$news['lang_id']}'>{$LANGDATA['L_NEWS_EDIT']}</a></li>";
    }
    //not translator
    if($config['NEWS_MULTIPLE_PAGES'] ) {
        if( (defined('ACL') && $acl_auth->acl_ask("admin_all||news_admin"))
                || (!defined('ACL') && $user['isAdmin'] == 1)
                || ($news['author'] == $user['username'])
        ) { 
            $content .= "<li><a rel='nofollow' href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&newpage=1'>{$LANGDATA['L_NEWS_NEW_PAGE']}</a></li>";        
        }
    }
    // TRANSLATE ADMIN, ANON IF, REGISTERED IF
    if(  (defined('MULTILANG')) && ( $config['NEWS_ANON_TRANSLATE']
            ||  (defined('ACL') && $acl_auth->acl_ask("admin_all||news_admin"))
            ||  (defined('ACL') && $config['NEWS_TRANSLATE_REGISTERED'] && $acl_auth->acl_ask("registered_all"))
            ||  (!defined('ACL') && $user['isAdmin'] == 1)
            ||  (!defined('ACL') && $config['NEWS_TRANSLATE_REGISTERED'] && !empty($user)) //NO_ACL registered
            )
    ) {        
        $content .= "<li><a rel='nofollow' href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&page={$news['page']}&news_new_lang={$news['nid']}&lang_id={$news['lang_id']}'>{$LANGDATA['L_NEWS_NEWLANG']}</a></li>";
    }
    //REST ONLY ADMIN
    if( (defined('ACL') && $acl_auth->acl_ask("admin_all||news_admin"))
        || (!defined('ACL') && $user['isAdmin'] == 1) 
    ) {
        if ($news['featured'] == 1 && $news['page'] == 1) {
            $content .= "<li><a class='link_active' rel='nofollow' href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_featured=0&featured_value=0&lang_id={$news['lang_id']}&admin=1''>{$LANGDATA['L_NEWS_FEATURED']}</a></li>";    
        } else if ($news['page'] == 1) {
            $content .= "<li><a rel='nofollow' href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_featured=1&featured_value=1&lang_id={$news['lang_id']}&admin=1''>{$LANGDATA['L_NEWS_FEATURED']}</a></li>";    
        }
        if ($news['moderation'] && $news['page'] == 1) {
            $content .= "<li><a rel='nofollow' href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_approved={$news['nid']}&lang_id={$news['lang_id']}&admin=1'>{$LANGDATA['L_NEWS_APPROVED']}</a></li>";
        }        
        //TODO  Add a menu for enable/disable news
        //$content .= "<li><a href=''>{$LANGDATA['L_NEWS_DISABLE']}</a></li>";
        if ($news['page'] == 1) {
            $content .= "<li><a rel='nofollow' href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_delete=1&admin=1&backlink=home' onclick=\"return confirm('{$LANGDATA['L_NEWS_CONFIRM_DEL']}')\">{$LANGDATA['L_NEWS_DELETE']}</a></li>";        
        }
        if ($config['NEWS_SELECTED_FRONTPAGE'] && $news['page'] == 1){
            if ($news['frontpage'] == 1) {
                $content .= "<li><a class='link_active' rel='nofollow' href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_frontpage=0&lang_id={$news['lang_id']}'>{$LANGDATA['L_NEWS_FRONTPAGE']}</a></li>";        
            } else {
                $content .= "<li><a rel='nofollow' href='/newspage.php?nid={$news['nid']}&lang={$news['lang']}&news_frontpage=1&lang_id={$news['lang_id']}'>{$LANGDATA['L_NEWS_FRONTPAGE']}</a></li>";
            }
        }
    }
    return $content;
}

function news_pager($news_page) {
    global $db, $config;
    
    $query = $db->select_all("news", array("nid" => $news_page['nid'], "lang_id" => $news_page['lang_id']));
    if( ($num_pages = $db->num_rows($query)) <= 1) {
        return false;
    }
    $content = "<div id='pager'><ul>";
    if ($config['FRIENDLY_URL']) {
        $friendly_title = news_friendly_title($news_page['title']);
        $news_page['page'] == 1 ? $a_class = "class='active'" : $a_class = "";
        $content .= "<li><a $a_class href='/{$news_page['lang']}/news/{$news_page['nid']}/1/$friendly_title'>1</a></li>";
    } else {
        $news_page['page'] == 1 ? $a_class = "class='active'" : $a_class = "";
        $content .= "<li><a $a_class href='newspage.php?nid={$news_page['nid']}&lang={$news_page['lang']}&page=1'>1</a></li>";
    }

    $pager = page_pager($config['NEWS_PAGER_MAX'], $num_pages, $news_page['page']);

    for ($i = $pager['start_page']; $i < $pager['limit_page']; $i++) {
        if ($config['FRIENDLY_URL']) {
            $friendly_title = news_friendly_title($news_page['title']);
            $news_page['page'] == $i ? $a_class = "class='active'" : $a_class = "";
            $content .= "<li><a $a_class href='/{$news_page['lang']}/news/{$news_page['nid']}/$i/$friendly_title'>$i</a></li>";
        } else {
            $news_page['page'] == $i ? $a_class = "class='active'" : $a_class = "";
            $content .= "<li><a $a_class href='newspage.php?nid={$news_page['nid']}&lang={$news_page['lang']}&page=$i'>$i</a></li>";
        }
    }
    if ($config['FRIENDLY_URL']) {
        $friendly_title = news_friendly_title($news_page['title']);
        $news_page['page'] == $num_pages ? $a_class = "class='active'" : $a_class = "";
        $content .= "<li><a $a_class href='/{$news_page['lang']}/news/{$news_page['nid']}/$num_pages/$friendly_title'>$num_pages</a></li>";
    } else {
        $news_page['page'] == $num_pages ? $a_class = "class='active'" : $a_class = "";
        $content .= "<li><a $a_class href='newspage.php?nid={$news_page['nid']}&lang={$news_page['lang']}&page=$num_pages'>$num_pages</a></li>";
    }        
    $content .= "</ul></div>";
    
    return $content;
}

function page_pager($max_pages, $num_pages, $actual_page) {    
    $addition = 0;
    $middle =  (round(($max_pages/2), 0, PHP_ROUND_HALF_DOWN) ); 
    $start_page = $actual_page - $middle ;
    
    if( $start_page < 2) {
        if($start_page < 0) {
            $addition = ($start_page * -1) +2 ;
        } else if ($start_page == 0) {
            $addition = $start_page + 2;
        } else {
            $addition = $start_page;
        }
        $start_page = 2;
    }   
    
    $limit_page = $actual_page  + $middle + $addition;
    $limit_page > $num_pages ? $limit_page = $num_pages : false;
    
    if( ($max_pages + $start_page) > $limit_page) {        
        $start_page = $start_page - (($max_pages + $start_page) - $limit_page);
    }
    $start_page < 2 ? $start_page = 2: false;
    
    $pager['start_page'] = $start_page;
    $pager['limit_page'] = $limit_page;
    
    return $pager;
}
function news_delete($nid, $lang_id) {
    global $db;

    $db->delete("news", array("nid" => $nid, "lang_id" => $lang_id));
            
    $query = $db->select_all("news", array("nid" => $nid), "LIMIT 1"); //check if other lang
    if ($db->num_rows($query) <= 0) {
        $db->delete("links", array("plugin" => "Newspage", "source_id" => $nid));
        //ATM by default this fuction delete all "links" if no exists the same news in other lang, mod like 
        //NewsMedia not need clean his "links"
        do_action("news_delete_mod", $nid);         
    } 
    return true;
}

function news_approved($nid, $lang_id) {
    global $db;
    
    if (empty($nid) || empty($lang_id) ) {
        return false;
    }    
    $db->update("news", array("moderation" => 0), array("nid" => $nid, "lang_id" => $lang_id));        

    return true;    
}

function news_featured($nid, $featured, $lang_id) {
    global $db;
    
    if (empty($nid) || empty($lang_id)) {
        return false;
    }
    $featured == 1 ? news_clean_featured($lang_id) : false;
    $db->update("news", array("featured" => $featured), array("nid" => $nid, "lang_id" => $lang_id));

    return true;    
}

function news_frontpage($nid, $lang_id, $frontpage_state) {
    global $db;

    empty($frontpage_state) ? $frontpage_state = 0 : false;

    if (empty($nid) || empty($lang_id) || $nid <= 0 && $lang_id <= 0) {            
        return false;
    }
    $db->update("news", array("frontpage" => $frontpage_state), array("nid" => $nid, "lang_id" => $lang_id));
    
    return true;    
}

function  news_stats($nid, $lang, $page, $visits) {
    global $db, $config;
    // NOT WORK
    //$db->update("news", array("visits" => "visits + 1 "), array("nid" => "$nid", "lang" => "$lang", "page" => "$page"), "LIMIT 1");
    $db->update("news", array("visits" => ++$visits), array("nid" => "$nid", "lang" => "$lang", "page" => "$page"), "LIMIT 1");
    if($config['NEWS_ADVANCED_STATS']) {
        news_adv_stats($nid, $lang);
    }
}

function news_adv_stats($nid, $lang) {
    global $db, $sm;
    
    $plugin = "Newspage";
    
    $user = $sm->getSessionUser();
    empty($user) ? $user['uid'] = 0 : false; //Anon        
    $ip = S_SERVER_REMOTE_ADDR();        
    $hostname = gethostbyaddr($ip);
    $where_ary = array( 
        "type" => "user_visits_page",
        "plugin" => "$plugin", 
        "lang" => "$lang", 
        "rid" => "$nid", 
        "uid" => $user['uid']
    );            
    $user['uid'] == 0 ? $where_ary['ip'] = $ip : false;
    
    $query = $db->select_all("adv_stats", $where_ary, "LIMIT 1");
    
    $user_agent = S_SERVER_USER_AGENT();
    $referer = S_SERVER_URL("HTTP_REFERER");
    


    if ($db->num_rows($query) > 0) {
        $user_adv_stats = $db->fetch($query);
        $counter = ++$user_adv_stats['counter'];
        //$db->update("adv_stats", array("counter" => "$counter", "user_agent" => "$user_agent", "referer" => "$referer"), array("plugin" => "$plugin", "lang" => "$lang", "rid" => "$nid", "uid" => $user['uid'], "ip" => "$ip"));
        $db->update("adv_stats", array("counter" => "$counter", "user_agent" => "$user_agent", "referer" => "$referer"), array("advstatid" => $user_adv_stats['advstatid']));
    } else{
        $insert_ary = array(
            "plugin" => "$plugin", 
            "type" => "user_visits_page",
            "rid" => "$nid", 
            "lang" => "$lang", 
            "uid" => $user['uid'], 
            "ip" => "$ip", 
            "hostname" => $hostname, 
            "user_agent" => "$user_agent", 
            "referer" => "referer",
            "counter" => 1
            );
        $db->insert("adv_stats", $insert_ary );
    }
    
    if( (!empty($referer)) && ( (strpos($referer, $_SERVER['SERVER_NAME']) )  === false) ) {
        $query = $db->select_all("adv_stats", array("type" => "referers_only", "referer" => "$referer"), "LIMIT 1");
        if ($db->num_rows($query) > 0) {
            $allreferers = $db->fetch($query);
            $counter = ++$allreferers['counter'];
            $db->update("adv_stats", array("counter" => "$counter") , array("advstatid" => $allreferers['advstatid']) );
        } else {
            $insert_ary = array(
                "plugin" => "$plugin",
                "type" => "referers_only",
                "referer" => $referer,
                "counter" => 1,
            );
            $db->insert("adv_stats", $insert_ary );
        }
    }    
    
}
function news_add_social_meta($news) {
    global $tpl, $config;
    $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
    $news['url'] = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $news['web_title'] = $config['TITLE'];
    $content = $tpl->getTPL_file("Newspage", "NewsSocialmeta", $news);
    $tpl->addto_tplvar("META", $content);
}