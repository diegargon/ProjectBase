<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function print_debug($msg, $filter = null) {
    global $debug;
    if ((array_search($msg, array_column($debug, 'msg')) ) != null) { //avoid duplicates
        return;
    }
    if (!empty($filter) && defined($filter) && defined('DEBUG')) {
        $debug[] = [ "msg" => "$msg", "filter" => "$filter" ];
    } else if (empty($filter) && defined('DEBUG')) {
        $debug[] = [ "msg" => "$msg", "filter" => "DEBUG" ];
    }
}

function getserverload() { // Return server load respect cpu's number 1.0 = 100% all cores
    $load = sys_getloadavg();
    $cmd = "cat /proc/cpuinfo | grep processor | wc -l";
    $num_cpus = trim(shell_exec($cmd));

    if (empty($load[0]) || empty($num_cpus)) {
        return false;
    }
    $current_load = round($load[0] / $num_cpus, 2);

    return $current_load;
}

function its_server_stressed() {
    global $cfg;

    if (($current_load = getserverload()) != false) {
        if ($current_load >= $cfg['SERVER_STRESS']) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

function codetovar($path, $data = null) {
    global $cfg, $LNG, $tpl;

    $tpldata = $tpl->get_tpldata();
    ob_start();
    include ($path);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

function format_date($date, $timestamp = false) {
    global $cfg;
    if ($timestamp) {
        return date($cfg['DEFAULT_DATEFORMAT'], $date);
    } else {
        return date($cfg['DEFAULT_DATEFORMAT'], strtotime($date));
    }
}

function includePluginFiles($plugin, $admin = 0) {
    global $cfg, $LNG;

    $class_file = "";
    $inc_file = "";

    //CONFIG FILES
    $cfg_plugin = "plugins/$plugin/$plugin.config.php";
    $cfg_plugin_user = "config/$plugin.config.php";
    file_exists($cfg_plugin) ? require_once($cfg_plugin) : false;
    file_exists($cfg_plugin_user) ? require_once($cfg_plugin_user) : false;  //User Overdrive
    //LANG FILES;
    $lang_file = "plugins/$plugin/lang/" . $cfg['WEB_LANG'] . "/$plugin.lang.php";
    file_exists($lang_file) ? include_once($lang_file) : false;

    //INC FILE
    if ($admin == 0) {
        $inc_file = "plugins/$plugin/includes/$plugin.inc.php";
        $class_file = "plugins/$plugin/includes/$plugin.class.php";
    } else {
        $inc_file = "plugins/$plugin/admin/$plugin.admin.inc.php";
    }
    !empty($inc_file) && file_exists($inc_file) ? include_once($inc_file) : false;
    !empty($inc_file) && file_exists($class_file) ? include_once($class_file) : false;
}

function remote_check($url) {

    if ((strpos($url, 'http://') !== 0) && (strpos($url, 'https://') !== 0)) {
        $url = "http://" . $url;
    }

    if (strpos($url, 'https://') !== 0) {
        stream_context_set_default( ['https' => [ 'method' => 'HEAD' ] ]);
    } else {
        stream_context_set_default( ['http' => [ 'method' => 'HEAD' ] ]);
    }

    $host = parse_url($url, PHP_URL_HOST);
    //FIX: gethostbyname sometimes not reliable, sometimes or in some servers resolv things like this http://jeihfw and return a IP :/ :?
    if (gethostbyname($host) === $host) { //get host resolv ip if fail return the host
        return false;
    } else {
        defined('DEBUG') ? $headers = get_headers($url) : $headers = @get_headers($url);
        if ($headers['0'] == 'HTTP/1.1 404 Not Found') {
            return false;
        }
    }
    return true;
}

function getLib($libname, $version) {
    //1.0 to 1.9 minor version must be 100% compatible
    //FIX  if ask for 0.9 and only exist < .9  actually load a minor version
    $LIBPATH = "libs/";

    if (empty($libname) || !isset($version)) { //can be 0 to 0.*
        return false;
    }

    if (preg_match("/./", $version)) {
        $v_mayor_minor = explode(".", $version);
    } else {
        $v_mayor_minor[0] = $version;
    }

    $libs = glob($LIBPATH . $libname . "-" . $v_mayor_minor[0] . "*", GLOB_ONLYDIR);
    if (empty($libs)) {
        return false;
    }
    $lib = end($libs); // GLOB SORT END element its the greater minor

    if (file_exists($lib . "/" . $libname . ".php")) {
        require_once ($lib . '/' . $libname . '.php');
    } else {
        return false;
    }
    return true;
}

function botDetect($match_type = 0) {
    global $cfg;

    if ($match_type == 1) {
        $botList = $cfg['BAD_BOTS'];
    } else if ($match_type == 2) {
        $botList = $cfg['WELCOME_BOTS'];
    } else {
        $botList = $cfg['WELCOME_BOTS'] . "|" . $cfg['BAD_BOTS'];
    }

    preg_match("/$botList/i", S_SERVER_USER_AGENT(), $matches);

    return (empty($matches)) ? false : true;
}
