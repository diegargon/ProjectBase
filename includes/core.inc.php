<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */
global $debug;


//global $htmllink;

require_once "config/config.inc.php";
require_once "includes/plugins.inc.php";
require_once "includes/plugins.check.php";

do_action("core_action");



function print_debug($msg) {
    global $debug;
    
    $debug .= $msg;
}

function text_echo($text) {
    return htmlspecialchars($text); 
}

function s_char($char, $size) {

    if (strlen($char) <= $size) {
        return input_filter($char);
    } else if ($size == 0) { // 0 disable size
        return input_filter($char); 
    }
    return false;
}

function s_num($num, $size) {
    if(is_numeric($num) && (strlen($num) <= $size)) {
        return $num;
    }
    return false;
}

function s_bool($bool) {
    return filter_var($bool, FILTER_VALIDATE_BOOLEAN);
}

function getserverload() {
  if(file_exists("/proc/loadavg")) {
         $load = file_get_contents("/proc/loadavg");
         $load = explode(' ', $load);
         return $load[0];
  }
	return 0;
}

function codetovar($path, $data) {
    ob_start();
    include ($path);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

function input_filter($data) {
    global $config;
    
    if (is_array($data)) {
        foreach ($data as $key => $element) {
            $data[$key] = input_filter($element);
        }
    } else {
        
        $data = trim(htmlentities(strip_tags($data)));        
        if(get_magic_quotes_gpc()) $data = stripslashes($data);
        
        if (isset($config['SQL_DB']) && $config['SQL_DB']) {
            $data = db_escape_string($data);
        }
        
    }
    
    return $data;
}

/* BORRAR
function sort_array_asc(&$thearray) {
    
    
    usort($thearray, function($a, $b) {
        return $a->priority - $b->priority;
    });    
}

function sort_array_desc(&$thearray) {
    
    
    usort($thearray, function($a, $b) {
        return $b->priority - $a->priority;
    });    
}
 * 
 * //
 */