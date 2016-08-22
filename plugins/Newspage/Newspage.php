<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Newspage_init(){
    global $tpl;
    print_debug("Newspage Inititated", "PLUGIN_LOAD");

    includePluginFiles("Newspage");
    $tpl->getCSS_filePath("Newspage");
    $tpl->getCSS_filePath("Newspage", "Newspage-mobile");

    if (news_check_display_submit()) {
        register_action("nav_element", "news_menu_submit_news");
    }
}

function news_portal() {    
    global $config, $tpl;
    require_once("includes/news_portal.php");
    do_action("news_portal_begin");
    
    do_action("common_web_structure");
    
    if ($config['LAYOUT_SWITCH']) {
        $news_nLayout = news_layout_select();
        $news_layout_tpl = "news_body_style" . $news_nLayout++;        
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
