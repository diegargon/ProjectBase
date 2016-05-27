<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */

function Newspage_AdminInit() {
    register_action("add_admin_menu", "Newspage_AdminMenu", "5"); 
}

function Newspage_AdminMenu($params) {
    $tab_num = 101; //TODO: A WAY TO ASSIGN UNIQ NUMBERS
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_content", "Newspage_AdminContent");        
        return "<li class='tab_active'><a href='?admtab=$tab_num'>Newspage</a></li>";
    } else {
        return "<li><a href='?admtab=$tab_num'>Newspage</a></li>";
    }
}

function Newspage_AdminContent() {
    return "<p>Hello from Newspage</p>";
}