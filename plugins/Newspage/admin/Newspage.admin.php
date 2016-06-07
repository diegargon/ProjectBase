<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

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

function Newspage_AdminContent($params) {
   global $LANGDATA, $config;    
   
   includePluginFiles("Newspage", 1);
    addto_tplvar("ADM_ASIDE_OPTION", "<li><a href='?admtab=" . $params['admtab'] ."&opt=1'>". $LANGDATA['L_PL_STATE'] ."</a></li>\n" );                               
    addto_tplvar("ADM_ASIDE_OPTION", "<li><a href='?admtab=" . $params['admtab'] ."&opt=2'>". $LANGDATA['L_NEWS_MODERATION'] ."</a></li>\n");
    addto_tplvar("ADM_ASIDE_OPTION", "<li><a href='?admtab=" . $params['admtab'] ."&opt=3'>". $LANGDATA['L_NEWS_CATEGORY'] ."</a></li>\n");
    if ($config['NEWS_SELECTED_FRONTPAGE']) {
        addto_tplvar("ADM_ASIDE_OPTION", "<li><a href='?admtab=" . $params['admtab'] ."&opt=4'>". $LANGDATA['L_NEWS_INFRONTPAGE'] ."</a></li>\n");
    }
    addto_tplvar("ADM_ASIDE_OPTION", do_action("ADD_ADM_GENERAL_OPT") );

    addto_tplvar("ADM_CONTENT_H1", "Newspage");
    $opt = S_GET_INT("opt");
    if ( $opt == 1 || $opt == false) {
        addto_tplvar("ADM_CONTENT_H2", $LANGDATA['L_GENERAL'] .": ".  $LANGDATA['L_PL_STATE'] );
        addto_tplvar("ADM_CONTENT", Admin_GetPluginState("Newspage"));                               
    } else if ($opt == 2) {
        addto_tplvar("ADM_CONTENT", Newspage_AdminModeration());              
    } else if ($opt == 3) {        
        if (isset($_POST['ModCatSubmit'])) {
            Newspage_ModCategories(); //Intercept modifify categories form
        }
        if (isset($_POST['NewCatSubmit'])) {
            Newspage_NewCategory(); //Intercept new categories form
        }        
        addto_tplvar("ADM_CONTENT", Newspage_AdminCategories()); 
    } else if ($opt == 4) {
        addto_tplvar("ADM_CONTENT", Newspage_InFrontpage());
    }
     
    return getTPL_file("Admin", "admin_std_content");
}
