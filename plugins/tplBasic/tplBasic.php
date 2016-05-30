<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

global $tpldata;

function tplBasic_init(){   
    if (DEBUG_PLUGINS_LOAD) { print_debug ("tplBasic initialized<br>"); }
    
    includePluginFiles("tplBasic");
    
    getCSS_filePath("tplBasic", "basic");
    getCSS_filePath("tplBasic", "basic-mobile");
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
    global $tpldata, $LANGDATA;
            
    if(empty($tpldata['E_TITLE'])) {
        $tpldata['E_TITLE'] = $LANGDATA['L_E_ERROR'];
    }
    if(empty($tpldata['E_BACKLINK_TITLE'])) {
        $tpldata['E_BACKLINK_TITLE'] = $LANGDATA['L_E_BACKLINK_TITLE'];       
    }    
    do_action("common_web_structure");        
    addto_tplvar("ADD_TO_BODY", getTPL_file("tplBasic", "error"));
   
 }

 function tplBasic_error_box () {
    global $tpldata, $LANGDATA;
    
    if(empty($tpldata['E_TITLE'])) {
        $tpldata['E_TITLE'] = $LANGDATA['L_E_ERROR'];
    }
    if(empty($tpldata['E_BACKLINK_TITLE'])) {
        $tpldata['E_BACKLINK_TITLE'] = $LANGDATA['L_E_BACKLINK_TITLE'];       
    }   
    addto_tplvar("ADD_TO_BODY", getTPL_file("tplBasic", "error"));
 }
 
function tpl_basic_head() {
    return getTPL_file("tplBasic", "head");
}

function tpl_basic_body() {
    return getTPL_file("tplBasic", "body");    
}

function tpl_basic_footer() {
    return getTPL_file("tplBasic", "footer");
}
