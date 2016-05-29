<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Newspage_init(){  
    if (DEBUG_PLUGINS_LOAD) { print_debug("Newspage Inititated<br/>");}
    
    includePluginFiles("Newspage"); 
    getCSS_filePath("Newspage");
    getCSS_filePath("Newspage", "Newspage-mobile");  
}

function news_main_page (){
    global $tpldata;
    
    $news_layout = news_layout_select();
    $tpldata['FEATURED'] = get_news_featured(1);
    $tpldata['COL1_ARTICLES'] = get_news(1,0);
    $tpldata['COL2_ARTICLES'] = get_news(1,0);
    $tpldata['COL3_ARTICLES'] = get_news(1,0);          
    
    if ($news_layout == 0 ) {        
        $l_switch = 1;
        $news_layout_tpl = "News_body_style1";
    } else {
        $l_switch = 0;        
        $news_layout_tpl = "News_body_style2";                                           
    }   
   
    addto_tplvar("NAV_ELEMENT", news_layout_switcher($l_switch));        
    addto_tplvar("POST_ACTION_ADD_TO_BODY", getTPL_file("Newspage", $news_layout_tpl));                                
    
}

function news_page() {
    global $tpldata, $config, $LANGDATA;
    
    if( ($nid = S_VAR_INTEGER($_GET['nid'], 8, 1)) == false) {
        $tpldata['E_MSG'] = $LANGDATA['L_NEWS_NOT_EXIST'];
        addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box"));
        return false;
    }
    if (($row = get_news_byId($nid, $config['WEB_LANG'])) == 403) {
        $tpldata['E_MSG'] = $LANGDATA['L_ERROR_NOACCESS'];
        addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box")); 
        return false; 
    } else if ($row == false) {
        if( ($row = get_news_byId($nid)) == false) {
            $tpldata['E_MSG'] = $LANGDATA['L_NEWS_NOT_EXIST'];
            addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box"));
            return false;
        } else {
            $tpldata['E_MSG'] = $LANGDATA['L_NEWS_WARN_NOLANG'];
            addto_tplvar("POST_ACTION_ADD_TO_BODY",  do_action("error_message_box"));
            return false;
            
        }        
    }
    
    $tpldata['NID'] = $row['nid'];    
    $tpldata['NEWS_TITLE'] = $row['title'];    
    $tpldata['NEWS_LEAD'] = $row['lead'];    
    $tpldata['NEWS_URL'] = "news.php?nid=$row[nid]";
    $tpldata['NEWS_DATE'] = format_date($row['date']);
    $tpldata['NEWS_AUTHOR'] = $row['author'];
    $tpldata['NEWS_TEXT']  = $row['text'];

    if ( ($allmedia = get_news_media_byID($nid)) != false) {
        foreach ($allmedia as $media) {
            if($media['itsmain'] == 1 ) {
                $tpldata['NEWS_MAIN_MEDIA'] = $media['medialink'];
            }
        }
    }
    addto_tplvar("POST_ACTION_ADD_TO_BODY", getTPL_file("Newspage", "news_show_body"));                                               
}
