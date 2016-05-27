<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Newspage_init(){  
    if (DEBUG_PLUGINS_LOAD) { print_debug("Newspage Inititated<br/>");}
    
    includePluginFiles("Newspage");     
}

function news_add_link (){
    $link = "";
    $link .= tpl_get_file("css", "Newspage", "");
    $link .= tpl_get_file("css", "Newspage", "Newspage-mobile");

    return $link;
}

function news_main_body_select (){
    global $tpldata;
    if(!empty($_POST['news_switch'])) { 
        $news_switch = S_VAR_INTEGER($_POST['news_switch'],1);
    } else{
        $news_switch = 0;
    }
    if ($news_switch == 1) {
        $l_switch = 0;
        register_action("add_to_body", "news_body_style2", "5");
    } else {
        $l_switch = 1;
        register_action("add_to_body", "news_body_style1", "5");        
    }
    if(isset($tpldata['ADD_TOP_NEWS'])) { //FIX: Change switcher to other location ¿nav?
        $tpldata['ADD_TOP_NEWS'] .= news_layout_switcher($l_switch);
    } else{
        $tpldata['ADD_TOP_NEWS'] = news_layout_switcher($l_switch);
    }    
}
function news_body_style1() {
    global $tpldata;
    $tpldata['FEATURED'] = get_news_featured(1);
    $tpldata['COL1_ARTICLES'] = get_news(1,0);
    $tpldata['COL2_ARTICLES'] = get_news(1,0);
    $tpldata['COL3_ARTICLES'] = get_news(1,0);
 
    return tpl_get_file("tpl", "Newspage", "News_body_style1"); 
}

function news_body_style2() {
    global $tpldata;
    $tpldata['FEATURED'] = get_news_featured(1);
    $tpldata['COL1_ARTICLES'] = get_news(1,0);
    $tpldata['COL2_ARTICLES'] = get_news(1,0);
    $tpldata['COL3_ARTICLES'] = get_news(1,0);
        
    return tpl_get_file("tpl", "Newspage", "News_body_style2");
}

function news_layout_switcher($value) {        
    $data = "<form method=\"post\"><div class=\"\">";
    $data .= "<input type=\"submit\"  value=\"\" class=\"button_switch\" />";
    $data .= "<input type=\"hidden\" value=\"$value\" name=\"news_switch\"/>";
    $data .= "</div></form>";
    return $data;
}