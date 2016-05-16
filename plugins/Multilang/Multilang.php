<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */


function Multilang_init(){
    global $config;
    print_debug("Multilang Inititated<br/>");
    
    $request_uri = $_SERVER['REQUEST_URI'];
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);   
    if(!isset($lang)) {
        $lang = $config['WEB_LANG'];
    }

    if (isset($_GET['lang'])) {
        $lang = s_char($_GET['lang'], 2);
        $config['WEB_URL'] = $config['WEB_URL'] . "$lang/";
    } else {
        $request_uri = $lang . $request_uri ;
        $request_uri = $config['WEB_URL'] . $request_uri;        
        header('Location:' .$request_uri);
    }

   
    
    
}