<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function NewsAds_init() {     
    print_debug("NewsAds initiated", "PLUGIN_LOAD");
    includePluginFiles("NewsAds");   
    NewsAds_ShowAds();
}

function NewsAds_ShowAds () {
    global $cfg, $tpl;
    
    if ($cfg['newsads_main_ad']) {
        $ad_code = NewsAds_GetMainAd();
        $main_banner = "<aside class='center' id='main_banner'>" . $ad_code ."</aside>";
        $tpl->addto_tplvar("ADD_TO_NEWSSHOW_TOP", $main_banner);
        $tpl->addto_tplvar("ADD_TOP_NEWS", $main_banner );
        $tpl->addto_tplvar("ADD_TOP_SECTION", $main_banner );
    }
    
    if ( S_GET_INT("nid") && ($cfg['newsads_sponsors'] || $cfg['newsads_global_sponsors']) ) {
        $sponsors = NewsAdds_Sponsors();
        $sponsors = "<aside class='sponsors center'>" . $sponsors ."</aside>";
        !empty($sponsors) ? $tpl->addto_tplvar("ADD_TO_NEWS_SIDE_PRE", $sponsors) : false;                    
    }
}

function NewsAds_GetMainAd() {
    global $db;
    
    $query = $db->select_all("news_ads", [ "itsmain" => 1 ], "LIMIT 1" );
    $news_ad = $db->fetch($query);
    
    return $news_ad['ad_code'];
}

function NewsAdds_Sponsors() {
    global $db, $cfg;
    
    $sponsors = "";
    
    if ($cfg['newsads_sponsors']) {
        $nid = S_GET_INT("nid", 11, 1);
        if (!empty($nid)) {
            $where_ary = [
                "itsmain" => 0,
                "resource_id" => "$nid"            
            ];
            $query = $db->select_all("news_ads", $where_ary);
            if( ($db->num_rows($query)) > 0 ) {
                while ($sponsor_row = $db->fetch($query)) {
                    $sponsors .= $sponsor_row['ad_code'];
                }
            }                
        }
    }    
    
    if ($cfg['newsads_global_sponsors']) {
        $where_ary = [
            "itsmain" => 0,
            "resource_id" => 0
            ];        
        $query = $db->select_all("news_ads", $where_ary);
        if( ($db->num_rows($query)) > 0 ) {        
            while ($sponsor_row = $db->fetch($query)) {
                $sponsors .= $sponsor_row['ad_code'];
            }
        }
        unset($where_ary);
        $db->free($query);    
    }    
    
    return $sponsors;
}