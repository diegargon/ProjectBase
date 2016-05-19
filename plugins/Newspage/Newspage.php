<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */

function Newspage_init(){
    global $config;
    
    print_debug("Newspage Inititated<br/>");
    require("includes/Newspage.inc.php");
    include_once("lang/" . $config['WEB_LANG'] . "/Newspage.lang.php" );
    //echo $_SERVER['REQUEST_URI'] ."<br>";
}


function news_add_link (){
    if($CSSPATH = tpl_get_path("css", "Newspage", "")) {
        $link = "<link rel='stylesheet' href='/$CSSPATH'>\n";
    }
    if($CSSPATH = tpl_get_path("css", "Newspage", "Newspage-mobile")) {
       $link .= "<link rel='stylesheet' href='/$CSSPATH'>\n";
    }
    return $link;
}

function news_show() {
    register_action("add_to_body", "news_show_body_1", "5");
}

function news_show_body_1() {
    global $tpldata;
    global $config;
    global $LANGDATA;
    
    if(($nid = s_num($_GET['nid'], 8)) == 0) {
        return 0;
    }
    if (($row = get_news_byId($nid, $config['WEB_LANG'])) == false) {
        $row = get_news_byId($nid, "");
        $tpldata['NEWS_MSG'] = $LANGDATA['L_NEWS_WARN_NOLANG'];
    }
    $tpldata['NID'] = $row['nid'];    
    $tpldata['NEWS_TITLE'] = $row['title'];    
    $tpldata['NEWS_LEAD'] = $row['lead'];    
    $tpldata['NEWS_URL'] = "news.php?nid=$row[nid]";
    $tpldata['NEWS_DATE'] = format_date($row['date']);
    $tpldata['NEWS_AUTHOR'] = $row['author'];
    $tpldata['NEWS_TEXT']  = $row['text'];

    $allmedia = get_news_media_byID($nid);
    
    foreach ($allmedia as $media) {
        if($media['itsmain'] == 1 ) {
            $tpldata['NEWS_MAIN_MEDIA'] = $media['medialink'];
        }
    }
      
     if ($TPLPATH = tpl_get_path("tpl", "Newspage", "news_show_body")) {
        return codetovar($TPLPATH, "");
    }     
}
function news_body_switcher (){
    global $tpldata;
    if(!empty($_POST['news_switch'])) { 
        $news_switch = s_char($_POST['news_switch'],"1");
    } else{
        $news_switch = 0;
    }
    if ($news_switch == 1) {
        $l_switch = 0;
        register_action("add_to_body", "news_body_2", "5");

    } else {
        $l_switch = 1;
        register_action("add_to_body", "news_body_1", "5");
        
    }
    if(isset($tpldata['ADD_TOP_NEWS'])) {
        $tpldata['ADD_TOP_NEWS'] .= news_layout_switcher($l_switch);
    } else{
        $tpldata['ADD_TOP_NEWS'] = news_layout_switcher($l_switch);
    }
    
}
function news_body_1() {
    global $tpldata;
    
    $tpldata['COL1_ARTICLES'] = get_news(1,0,1,0);
    $tpldata['COL2_ARTICLES'] = get_news(1,0,1,0);
    $tpldata['COL3_ARTICLES'] = get_news(1,0,1,0);
    $tpldata['FEATURED'] = get_news(1,1,1,1);
   
    if ($TPLPATH = tpl_get_path("tpl", "Newspage", "News_body")) {
        return codetovar($TPLPATH, "");
    }        
}

function news_body_2() {
    global $tpldata;
   
    $tpldata['COL1_ARTICLES'] = get_news(1,0,1,0);
    $tpldata['COL2_ARTICLES'] = get_news(1,0,1,0);
    $tpldata['COL3_ARTICLES'] = get_news(1,0,1,0);
    $tpldata['FEATURED'] = get_news(1,1,1,1);
    
    if ($TPLPATH = tpl_get_path("tpl", "Newspage", "News_body_2")) {
        return codetovar($TPLPATH, "");
    }        
}


function news_layout_switcher($value) {        
    $data = "<form method=\"post\"><div class=\"\">";
    $data .= "<input type=\"submit\"  value=\"\" class=\"button_switch\" />";
    $data .= "<input type=\"hidden\" value=\"$value\" name=\"news_switch\"/>";
    $data .= "</div></form>";
    return $data;
}