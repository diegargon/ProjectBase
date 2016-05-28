<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SimpleACL_AdminInit() {
    register_action("add_admin_menu", "SimpleACL_AdminMenu", 5); 
}

function SimpleACL_AdminMenu($params) {
    $tab_num = 102; //TODO: A WAY TO ASSIGN UNIQ NUMBERS
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_content", "SimpleACL_AdminContent");        
        return "<li class='tab_active'><a href='?admtab=$tab_num'>SimpleACL</a></li>";
    } else {
        return "<li><a href='?admtab=$tab_num'>SimpleACL</a></li>";
    }
}

function SimpleACL_AdminContent() {
    return getTPL_file("SimpleACL", "acl_admin_main");
}