<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */

function SMBasic_AdminInit() {
    register_action("add_admin_menu", "SMBasic_AdminMenu", "5"); 
}

function SMBasic_AdminMenu($params) {
    $tab_num = 100; //TODO: A WAY TO ASSIGN UNIQ NUMBERS
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_content", "SMBasic_AdminContent");        
        return "<li class='tab_active'><a href='?admtab=$tab_num'>SMBasic</a></li>";
    } else {
        return "<li><a href='?admtab=$tab_num'>SMBasic</a></li>";
    }
}

function SMBasic_AdminContent() {
    return "<p>Hello from SMBasic</p>";
}
