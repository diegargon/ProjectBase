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

function mobileDetect() {
    $user_agent = S_SERVER_USER_AGENT();
    
    if (
            preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)'
                    . '|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0'
                    . '|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $user_agent) 
            ||
            preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)'
                    . '|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm'
                    . '|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)'
                    . '|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c'
                    . '|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu'
                    . '|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)'
                    . '|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)'
                    . '|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))'
                    . '|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/'
                    . '|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)'
                    . '|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)'
                    . '|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )'
                    . '|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($user_agent, 0, 4))
            ) {
        return true;
    } else {
        return false;
    }
}
