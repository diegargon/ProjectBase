<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function Newspage_init() {
    global $tpl;
    print_debug("Newspage Inititated", "PLUGIN_LOAD");

    includePluginFiles("Newspage");
    $tpl->getCSS_filePath("Newspage");
    $tpl->getCSS_filePath("Newspage", "Newspage-mobile");

    news_check_display_submit() ? register_action("header_menu_element", "news_menu_submit_news") : null;
}

function news_portal() {
    global $config, $tpl;

    require_once 'includes/news_portal.php';

    do_action("news_portal_begin");
    do_action("common_web_structure");

    if ($config['LAYOUT_SWITCH']) {
        $news_nLayout = news_layout_select();
        $news_layout_tpl = "news_portal_style" . $news_nLayout++;
        $tpl->addto_tplvar("news_nSwitch", $news_nLayout);
        register_action("header_menu_element", "news_layout_switcher", 6);
    } else {
        $news_layout_tpl = "news_portal_style1";
    }

    news_cat_menu();
    $portal_content = news_portal_content();
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", $news_layout_tpl, $portal_content));
}
