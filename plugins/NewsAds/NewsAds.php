<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsAds_init() {     
    print_debug("NewsAds initiated", "PLUGIN_LOAD");
    includePluginFiles("NewsAds");   
    NewsAds_ShowAds();
}

function NewsAds_ShowAds () {
    global $config, $tpl;
    
    if ($config['newsads_main_ad']) {
        $ad_code = NewsAds_GetMainAd();
        $tpl->addto_tplvar("ADD_TO_NEWSSHOW_TOP", $ad_code );
        $tpl->addto_tplvar("ADD_TOP_NEWS", $ad_code );
    }
    
    if ( S_GET_INT("nid") && ($config['newsads_sponsors'] || $config['newsads_global_sponsors']) ) {
        $sponsors = NewsAdds_Sponsors();
        !empty($sponsors) ? $tpl->addto_tplvar("ADD_TO_NEWS_SIDE", $sponsors) : false;                    
    }
}

function NewsAds_GetMainAd() {
    global $db;
    
    $query = $db->select_all("news_ads", array ("itsmain" => 1), "LIMIT 1");
    $news_ad = $db->fetch($query);
    
    return $news_ad['ad_code'];
}

function NewsAdds_Sponsors() {
    global $db, $config;
    
    $sponsors = "";
    
    if ($config['newsads_sponsors']) {
        $nid = S_GET_INT("nid", 11, 1);
        if (!empty($nid)) {
            $where_ary = array(
                "itsmain" => 0,
                "resource_id" => "$nid"            
            );
            $query = $db->select_all("news_ads", $where_ary);
            if( ($db->num_rows($query)) > 0 ) {
                while ($sponsor_row = $db->fetch($query)) {
                    $sponsors .= $sponsor_row['ad_code'];
                }
            }                
        }
    }    
    
    if ($config['newsads_global_sponsors']) {
        $where_ary = array(
            "itsmain" => 0,
            "resource_id" => 0
            );
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