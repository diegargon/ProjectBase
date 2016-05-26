<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

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

function codetovar($path, $data = null) {
    global $config, $tpldata, $LANGDATA;
    
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

function includeConfig($plugin) {
    global $config;
    $config_plugin = "plugins/$plugin/$plugin.config.php";
    $config_plugin_user = "config/$plugin.config.php";
    if (file_exists($config_plugin)) {
        require_once($config_plugin);
    }

    if (file_exists($config_plugin_user)) { //User Overdrive
        require_once($config_plugin_user); 
    }
}

function includeLang($plugin) {
    global $config;
    global $LANGDATA;
    $lang_file = "plugins/$plugin/lang/" . $config['WEB_LANG'] . "/$plugin.lang.php";
    if (file_exists($lang_file)) {
        include_once($lang_file);
    }       
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