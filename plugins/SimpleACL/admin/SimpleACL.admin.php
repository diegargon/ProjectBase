<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SimpleACL_AdminInit() {
    register_action("add_admin_menu", "SimpleACL_AdminMenu", 5); 
}

function SimpleACL_AdminMenu($params) {
    //TODO A way to assign uniq numbers
    $tab_num = 102;
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_content", "SimpleACL_AdminContent");        
        return "<li class='tab_active'><a href='?admtab=$tab_num'>SimpleACL</a></li>";
    } else {
        return "<li><a href='?admtab=$tab_num'>SimpleACL</a></li>";
    }
}

function SimpleACL_AdminContent() {
    global $tpl;    
    return $tpl->getTPL_file("SimpleACL", "acl_admin_main");
}