<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Multilang_AdminInit() {
    register_action("add_admin_menu", "Multilang_AdminMenu", 5); 
}

function Multilang_AdminMenu($params) {
    $tab_num = 102; //TODO: A WAY TO ASSIGN UNIQ NUMBERS
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_content", "Multilang_AdminContent");        
        return "<li class='tab_active'><a href='?admtab=$tab_num'>Multilang</a></li>";
    } else {
        return "<li><a href='?admtab=$tab_num'>Multilang</a></li>";
    }
}

function Multilang_AdminContent() {
    return "<p>Hello from Multilang";
//    return getTPL_file("Multilang", "acl_admin_main");
}