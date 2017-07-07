<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function Multilang_init(){
    global $cfg, $ml;

    print_debug("Multilang Inititated", "PLUGIN_LOAD");
    includePluginFiles("Multilang");

    $ml = new Multilang;

    $request_uri = S_SERVER_REQUEST_URI();

    if($cfg['ML_FORCEUSE_DFL_LANG']) {
        $lang = $cfg['WEB_LANG'];
        if ($cfg['FRIENDLY_URL']) {
            $cfg['WEB_URL'] = $cfg['WEB_URL'] . "$lang/";
        } else {
            $cfg['WEB_URL'] = $cfg['WEB_URL'] . "?lang=" . $cfg['WEB_LANG'];
        }
    } else {
        if ( (isset($_GET['lang'])) &&  (($lang = S_VAR_CHAR_AZ($_GET['lang'], 2, 2) ) != false)) {
                $cfg['WEB_URL'] = $cfg['WEB_URL'] . "$lang/";
                $cfg['WEB_LANG'] = $lang;
        } else {
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
                $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                isset($lang) ? $cfg['WEB_LANG'] = $lang : false;
            } else {
                $lang = $cfg['WEB_LANG'];
            }
        }
    }

    if ($request_uri == '/' && !$cfg['ML_FORCEUSE_DFL_LANG']) {
        if ($cfg['FRIENDLY_URL']) {
            $request_uri = $cfg['WEB_URL'] . $cfg['WEB_LANG'] . "/";
        } else {
            $request_uri = $cfg['WEB_URL'] . "?lang=" . $cfg['WEB_LANG'];
        }
       // header('Location:' .$request_uri);
    }
    if(!$cfg['ML_FORCEUSE_DFL_LANG'] && isset($_POST['choose_lang']) && (($choose_lang = S_POST_CHAR_AZ("choose_lang", 2, 2)) != false)) {
        $request_uri = str_replace("/". $cfg['WEB_LANG'], "/".$choose_lang, $request_uri); // FIX better method than can replace something not wanted.
        $request_uri = str_replace("lang=". $cfg['WEB_LANG'], "lang=".$choose_lang, $request_uri);
        header('Location:' .$request_uri);
        exit;
    }
    !$cfg['ML_FORCEUSE_DFL_LANG'] ? register_action("header_menu_element", [ $ml, "get_nav" ], 6) : false;
}