<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function Newspage_AdminInit() {
    register_action("add_admin_menu", "Newspage_AdminMenu", "5");
}

function Newspage_AdminMenu($params) {
    $tab_num = 101; //TODO: A WAY TO ASSIGN UNIQ NUMBERS
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_content", "Newspage_AdminContent");
        return "<li class='tab_active'><a href='admin&admtab=$tab_num'>Newspage</a></li>";
    } else {
        return "<li><a href='admin&admtab=$tab_num'>Newspage</a></li>";
    }
}

function Newspage_AdminContent($params) {
    global $LANGDATA, $config, $tpl;

    includePluginFiles("Newspage", 1);
    $tpl->getCSS_filePath("Newspage");
    $tpl->getCSS_filePath("Newspage", "Newspage-mobile");

    $page_data['ADM_ASIDE_OPTION'] = "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=1'>" . $LANGDATA['L_PL_STATE'] . "</a></li>\n";
    $page_data['ADM_ASIDE_OPTION'] .= "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=2'>" . $LANGDATA['L_NEWS_MODERATION'] . "</a></li>\n";
    $page_data['ADM_ASIDE_OPTION'] .= "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=3'>" . $LANGDATA['L_NEWS_CATEGORY'] . "</a></li>\n";
    $page_data['ADM_ASIDE_OPTION'] .= "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=4'>" . $LANGDATA['L_NEWS_INFRONTPAGE'] . "</a></li>\n";
    $page_data['ADM_ASIDE_OPTION'] .= do_action("ADD_ADM_NEWSPAGE_OPT");

    $page_data['ADM_CONTENT_H1'] = "Newspage";
    $opt = S_GET_INT("opt");
    if ($opt == 1 || $opt == false) {
        $page_data['ADM_CONTENT_H2'] = $LANGDATA['L_GENERAL'] . ": " . $LANGDATA['L_PL_STATE'];
        $page_data['ADM_CONTENT'] = Admin_GetPluginState("Newspage");
    } else if ($opt == 2) {
        $page_data['ADM_CONTENT_H2'] = $LANGDATA['L_NEWS_MODERATION'];
        $page_data['ADM_CONTENT'] = $LANGDATA['L_NEWS_MODERATION_DESC'] . Newspage_AdminModeration();
    } else if ($opt == 3) {
        if (isset($_POST['ModCatSubmit'])) {
            Newspage_ModCategories(); //Intercept modifify categories form
        }
        if (isset($_POST['NewCatSubmit'])) {
            Newspage_NewCategory(); //Intercept new categories form
        }
        $page_data['ADM_CONTENT_H2'] = $LANGDATA['L_NEWS_CATEGORIES'];
        $page_data['ADM_CONTENT'] = $LANGDATA['L_NEWS_CATEGORY_DESC'] . Newspage_AdminCategories();
    } else if ($opt == 4) {
        $page_data['ADM_CONTENT_H2'] = $LANGDATA['L_NEWS_INFRONTPAGE'];
        $page_data['ADM_CONTENT'] = $LANGDATA['L_NEWS_INFRONTPAGE_DESC'] . Newspage_InFrontpage();
    } else {
        do_action("ADM_NEWSPAGE_OPT", $opt);
    }
    return $tpl->getTPL_file("Admin", "admin_std_content", $page_data);
}
