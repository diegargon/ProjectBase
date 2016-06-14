<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function tplBasic_init(){   
    global $tpl;    
    print_debug ("tplBasic initialized", "PLUGIN_LOAD");

    includePluginFiles("tplBasic");
    
    $tpl = new TPL;
   
    $tpl->getCSS_filePath("tplBasic", "basic");
    $tpl->getCSS_filePath("tplBasic", "basic-mobile");
    register_action("common_web_structure", "tplBasic_web_structure", "0");
    register_uniq_action("index_page", "tplBasic_index_page", "5");
    register_uniq_action("error_message_page", "tplBasic_error_page");
    register_uniq_action("error_message_box", "tplBasic_error_box");
}

function tplBasic_web_structure() {
    register_uniq_action("get_head", "tpl_basic_head");
    register_uniq_action("get_body", "tpl_basic_body");
    register_uniq_action("get_footer", "tpl_basic_footer");     
}

function tplBasic_index_page() {
    do_action("common_web_structure");        
}

function tplBasic_error_page() {        
    global $LANGDATA, $tpl;
            
    $tpl->add_if_empty("E_TITLE", $LANGDATA['L_E_ERROR']);        
    $tpl->add_if_empty("E_BACKLINK_TITLE", $LANGDATA['L_BACK']);  

    do_action("common_web_structure");        
    $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("tplBasic", "error"));
   
 }

 function tplBasic_error_box () {
    global $tpl, $LANGDATA;
    
    $tpl->add_if_empty("E_TITLE", $LANGDATA['L_E_ERROR']);
    $tpl->add_if_empty("E_BACKLINK_TITLE", $LANGDATA['L_BACK']);
            
    $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("tplBasic", "error"));
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
