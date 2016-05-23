<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */

global $debug;
global $external_scripts;
$debug = [];
$external_scripts =[];

//global $htmllink;

require_once "config/config.inc.php";
require_once "includes/valfilters.inc.php";
require_once "includes/plugins.inc.php";
require_once "includes/plugins.check.php";

do_action("core_action");

function print_debug($msg) {
    global $debug;
    
    $debug[] = $msg;
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

function format_date($date, $timestamp = false) {
    global $config;
    if ($timestamp) {
       return date($config['DEFAULT_DATEFORMAT'], $date);        
    } else {
       return date($config['DEFAULT_DATEFORMAT'], strtotime($date));
    }
}

function check_jsScript($script) {
    global $external_scripts;
        
    foreach ($external_scripts as $value) {

      if ($value == $script) {
            return true;
        }

    }
 
    return false;
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