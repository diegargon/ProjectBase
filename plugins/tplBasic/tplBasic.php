<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function tplBasic_init() {
    global $tpl, $config;
    print_debug("tplBasic initialized", "PLUGIN_LOAD");

    includePluginFiles("tplBasic");

    $custom_lang = "tpl/lang/" . $config['WEB_LANG'] . "/custom.lang.php";
    file_exists($custom_lang) ? require_once($custom_lang) : false;

    $tpl = new TPL;

    $tpl->getCSS_filePath("tplBasic", "basic");
    $tpl->getCSS_filePath("tplBasic", "basic-mobile");
    register_action("common_web_structure", "tplBasic_web_structure", "0");
    register_uniq_action("index_page", "tplBasic_index_page", "5");
    register_uniq_action("message_page", "tplBasic_message_page");
    register_uniq_action("message_box", "tplBasic_message_box");
}

function tplBasic_web_structure() {
    register_uniq_action("get_head", "tpl_basic_head");
    register_uniq_action("get_body", "tpl_basic_body");
    register_uniq_action("get_footer", "tpl_basic_footer");
}

function tplBasic_index_page() {
    do_action("common_web_structure");
}

function tplBasic_message_page($box_data) {
    do_action("message_box", $box_data);
    do_action("common_web_structure");
}

function tplBasic_message_box($box_data) {
    global $config, $tpl, $LANGDATA;
    
    !empty($box_data['title']) ? $data['BOX_TITLE'] = $LANGDATA[$box_data['title']] : $data['BOX_TITLE'] = $LANGDATA['L_E_ERROR'];
    !empty($box_data['backlink']) ? $data['BOX_BACKLINK'] = $box_data['backlink'] : $data['BOX_BACKLINK'] = $config['BACKLINK'];
    !empty($box_data['backlink_title']) ? $data['BOX_BACKLINK_TITLE'] = $LANGDATA[$box_data['backlink_title']] : $data['BOX_BACKLINK_TITLE'] = $LANGDATA['L_BACK'];
    $data['BOX_MSG'] = $LANGDATA[$box_data['MSG']];
    $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("tplBasic", "msgbox", $data));
}

function tpl_basic_head() {
    global $tpl;
    return $tpl->getTPL_file("tplBasic", "head");
}

function tpl_basic_body() {
    global $tpl;
    return $tpl->getTPL_file("tplBasic", "body");
}

function tpl_basic_footer() {
    global $tpl;
    return $tpl->getTPL_file("tplBasic", "footer");
}
