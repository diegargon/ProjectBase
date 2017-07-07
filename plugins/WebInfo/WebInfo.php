<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function WebInfo_init() {
    global $cfg;
    print_debug("WebInfo initiated", "PLUGIN_LOAD");
    includePluginFiles("WebInfo");
    WebInfo_footer();
    $cfg['WEBINFO_SHOWDATE'] ? register_action("add_to_body", "WebInfoDate", 8) : false;
}

function WebInfoDate() {
    global $tpl, $cfg;

    $locale = $cfg['WEB_LANG'] . "_" . strtoupper($cfg['WEB_LANG'] . "." . strtolower($cfg['CHARSET']));
    $oldLocale = setlocale(LC_TIME, $locale);
    $today = "<li class=\"nav_left date\">" . utf8_encode(strftime("%A %d %B %Y", time())) . "</li>";
    $tpl->addto_tplvar("HEADER_MENU_ELEMENT", $today);
    setlocale(LC_TIME, $oldLocale);
}

function WebInfo_footer() {
    global $tpl, $cfg, $LNG;

    $tpl->getCSS_filePath("WebInfo");

    if ($cfg['FRIENDLY_URL']) {
        $footer_data['footer_menu'] = '<li><a href="/' . $cfg['WEB_LANG'] . '/AboutUs">' . $LNG['L_WEBINF_ABOUTUS'] . '</a></li>';
        if ($cfg['WEBINFO_CONTACT_FORM']) {
            $footer_data['footer_menu'] .= '<li><a href="/' . $cfg['WEB_LANG'] . '/Contact">' . $LNG['L_WEBINF_CONTACT'] . '</a></li>';
        }
        if (defined('NEWSADS')) {
            $footer_data['footer_menu'] .= '<li><a href="/' . $cfg['WEB_LANG'] . '/Advertise">' . $LNG['L_WEBINF_ADVERTISE'] . '</a></li>';
        }
        $footer_data['footer_menu'] .= '<li><a href="/' . $cfg['WEB_LANG'] . '/Terms">' . $LNG['L_WEBINF_TOS'] . '</a></li>';
    } else {
        $footer_data['footer_menu'] = '<li><a href="'. $cfg['CON_FILE'] .' ?module=WebInfo&page=AboutUs&lang=' . $cfg['WEB_LANG'] . '>AboutUs</a></li>';
    }
    $tpl->addto_tplvar("ADD_TO_FOOTER", $tpl->getTPL_file("WebInfo", "footer_nav", $footer_data));
}