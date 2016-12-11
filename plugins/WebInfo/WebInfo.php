<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function WebInfo_init() {
    print_debug("WebInfo initiated", "PLUGIN_LOAD");
    includePluginFiles("WebInfo");
    WebInfo_footer();
}

function WebInfo_footer() {
    global $tpl, $config, $LANGDATA;
    
    $tpl->getCSS_filePath("WebInfo");

    if ($config['FRIENDLY_URL']) {
        $footer_data['footer_menu'] = '<li><a href="AboutUs">' . $LANGDATA['L_WEBINF_ABOUTUS'] . '</a></li>';
        if ($config['WEBINFO_CONTACT_FORM']) {
            $footer_data['footer_menu'] .= '<li><a href="Contact">' . $LANGDATA['L_WEBINF_CONTACT'] . '</a></li>';
        }
        $footer_data['footer_menu'] .= '<li><a href="Advertise">' . $LANGDATA['L_WEBINF_ADVERTISE'] . '</a></li>';
        $footer_data['footer_menu'] .= '<li><a href="Terms">' . $LANGDATA['L_WEBINF_TOS'] . '</a></li>';
    } else {
        $footer_data['footer_menu'] = '<li><a href="app.php?module=WebInfo&page=AboutUs&lang=' . $config['WEB_LANG'] . '>AboutUs</a></li>';
    }
    $tpl->addto_tplvar("ADD_TO_FOOTER", $tpl->getTPL_file("WebInfo", "footer_nav", $footer_data));
}