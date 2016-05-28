<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Multilang_AdminInit() {
    register_action("add_admin_menu", "Multilang_AdminMenu"); 
}

function Multilang_AdminMenu($params) {
    $tab_num = 103; //TODO: A WAY TO ASSIGN UNIQ NUMBERS
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_content", "Multilang_AdminContent");        
        return "<li class='tab_active'><a href='?admtab=$tab_num'>Multilang</a></li>";
    } else {
        return "<li><a href='?admtab=$tab_num'>Multilang</a></li>";
    }
}

function Multilang_AdminContent($params) {   
    global $tpldata, $LANGDATA;    
    $tpldata['ADM_ASIDE_OPTION'] = "<li><a href='?admtab=" . $params['admtab'] ."&opt=1'>". $LANGDATA['L_PL_STATE'] ."</a></li>\n";
    $tpldata['ADM_ASIDE_OPTION'] .=  "<li><a href='?admtab=" . $params['admtab'] ."&opt=2'>Opcion 2</a></li>\n";
    $tpldata['ADM_ASIDE_OPTION'] .= do_action("ADD_ADM_GENERAL_OPT");
    
    if ( (!$opt = S_GET_INT("opt")) || $opt == 1 ) {
        $tpldata['ADM_CONTENT_DESC'] = $LANGDATA['L_GENERAL'] .": ".  $LANGDATA['L_PL_STATE'];
        $tpldata['ADM_CONTENT'] = Admin_GetPluginState("Multilang");
    } else {
        $tpldata['ADM_CONTENT_DESC'] = $LANGDATA['L_GENERAL'] .": Other opt";
        $tpldata['ADM_CONTENT'] = "Content from other opt";
    }
    
    return getTPL_file("Admin", "admin_std_content");   
}