<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function WebInfo_init() {
    global $config;
    print_debug("WebInfo initiated", "PLUGIN_LOAD");
    includePluginFiles("WebInfo");
    WebInfo_footer();
    $config['WEBINFO_SHOWDATE'] ? register_action("add_to_body", "WebInfoDate", 8) : false;
}

function WebInfoDate() {
    global $tpl, $config;

    $locale = $config['WEB_LANG'] . "_" . strtoupper($config['WEB_LANG'] . "." . strtolower($config['CHARSET']));
    $oldLocale = setlocale(LC_TIME, $locale);
    $today = "<li class=\"nav_left date\">" . utf8_encode(strftime("%A %d %B %Y", time())) . "</li>";
    $tpl->addto_tplvar("HEADER_MENU_ELEMENT", $today);
    setlocale(LC_TIME, $oldLocale);
}

function WebInfo_footer() {
    global $tpl, $config, $LANGDATA;

    $tpl->getCSS_filePath("WebInfo");

    if ($config['FRIENDLY_URL']) {
        $footer_data['footer_menu'] = '<li><a href="/' . $config['WEB_LANG'] . '/AboutUs">' . $LANGDATA['L_WEBINF_ABOUTUS'] . '</a></li>';
        if ($config['WEBINFO_CONTACT_FORM']) {
            $footer_data['footer_menu'] .= '<li><a href="/' . $config['WEB_LANG'] . '/Contact">' . $LANGDATA['L_WEBINF_CONTACT'] . '</a></li>';
        }
        if (defined('NEWSADS')) {
            $footer_data['footer_menu'] .= '<li><a href="/' . $config['WEB_LANG'] . '/Advertise">' . $LANGDATA['L_WEBINF_ADVERTISE'] . '</a></li>';
        }
        $footer_data['footer_menu'] .= '<li><a href="/' . $config['WEB_LANG'] . '/Terms">' . $LANGDATA['L_WEBINF_TOS'] . '</a></li>';
    } else {
        $footer_data['footer_menu'] = '<li><a href="'. $config['CON_FILE'] .' ?module=WebInfo&page=AboutUs&lang=' . $config['WEB_LANG'] . '>AboutUs</a></li>';
    }
    $tpl->addto_tplvar("ADD_TO_FOOTER", $tpl->getTPL_file("WebInfo", "footer_nav", $footer_data));
}