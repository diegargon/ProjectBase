<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Multilang_init(){
    global $config, $ml, $tpl;
    
    print_debug("Multilang Inititated", "PLUGIN_LOAD");
    includePluginFiles("Multilang");      
    
    $ml = new Multilang;        
    
    $request_uri = S_SERVER_REQUEST_URI();
    
    if ( (isset($_GET['lang'])) &&
        (($lang = S_VAR_CHAR_AZ($_GET['lang'], 2, 2) ) != false)
        ) {
            $config['WEB_URL'] = $config['WEB_URL'] . "$lang/";
            $config['WEB_LANG'] = $lang;
    } else {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2); 
            isset($lang) ? $config['WEB_LANG'] = $lang : false;
        } else {
            $lang = $config['WEB_LANG'];
        }
    }   
    if ($request_uri == '/') {
        if ($config['FRIENDLY_URL']) {
            $request_uri = $config['WEB_URL'] . $config['WEB_LANG'] ."/";
        } else {
        $request_uri = $config['WEB_URL'] . "?lang=" . $config['WEB_LANG'];    
        }
        header('Location:' .$request_uri);
    }
    if( isset($_POST['choose_lang']) && (($choose_lang = S_POST_CHAR_AZ("choose_lang", 2, 2)) != false)) {         
        $request_uri = str_replace("/". $config['WEB_LANG'], "/".$choose_lang, $request_uri); // FIX better method than can replace something not wanted.
        $request_uri = str_replace("lang=". $config['WEB_LANG'], "lang=".$choose_lang, $request_uri);
        header('Location:' .$request_uri);
    }
    register_action("nav_element", array($ml, "get_nav"), 6);   
    $tpl->addto_tplvar("SCRIPTS_TOP", $ml->get_js());     
}
