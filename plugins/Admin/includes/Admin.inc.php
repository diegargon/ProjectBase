<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function admin_load_plugin_files() {
    //Load administration side from all register plugins (all enabled) and init the admin_init function.
    //TODO disable too? for enable?
    global $registered_plugins;

    foreach ($registered_plugins as $plugin) {
        print_debug("ADMIN: Admin processing $plugin->plugin_name", "ADMIN_DEBUG");
        if (!empty($plugin->function_admin_init)) {
            $admin_file = "plugins/$plugin->plugin_name/admin/$plugin->plugin_name.admin.php";
            if (file_exists($admin_file)) {
                require_once($admin_file);
                if (function_exists($plugin->function_admin_init)) {
                    $init_function = $plugin->function_admin_init;
                    $init_function();
                } else {
                    print_debug("ADMIN: Function $plugin->function_admin_init not exist", "ADMIN_DEBUG");
                }
            } else {
                print_debug("ADMIN: File $admin_file not exist", "ADMIN_DEBUG");
            }
        } else {
            print_debug("ADMIN: Plugin $plugin->plugin_name haven't the function admin_init declared in his json file", "ADMIN_DEBUG");
        }
    }
}

function Admin_GetPluginState($plugin) {
    global $registered_plugins, $tpl;
    $content = "";
    foreach ($registered_plugins as $reg_plugin) {
        if ($reg_plugin->plugin_name == $plugin) {
            $content = $tpl->getTPL_file("Admin", "plugin_state", (array) $reg_plugin);
        }
    }
    $content = $content . "<hr/><p><pre>" . htmlentities(Admin_GetPluginConfigFiles($plugin)) . "</pre></p>";
    return $content;
}

function Admin_GetPluginConfigFiles($plugin) { //TODO BETTER CONFIG VIEW
    $cfg_plugin = "plugins/$plugin/$plugin.config.php";
    $cfg_plugin_user = "config/$plugin.config.php";
    $data = "";

    file_exists($cfg_plugin) ? $data .= file_get_contents($cfg_plugin) : false;
    file_exists($cfg_plugin_user) ? $data .= file_get_contents($cfg_plugin_user) : false;  //User Overdrive                       

    return $data;
}

function Admin_generalContent($params) {
    global $LNG, $tpl;

    $page_data['ADM_ASIDE_OPTION'] = "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=1'>" . $LNG['L_PL_STATE'] . "</a></li>\n";
    $page_data['ADM_ASIDE_OPTION'] .= "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=2'>Php Info</a></li>\n";
    $page_data['ADM_ASIDE_OPTION'] .= do_action("ADD_ADM_GENERAL_OPT");

    if ((!$opt = S_GET_INT("opt")) || $opt == 1) {
        $page_data['ADM_CONTENT'] .= Admin_GetPluginState("Admin");
        $page_data['ADM_CONTENT'] .= "<hr/><p><pre>" . htmlentities(Admin_GetPluginConfigFiles("Admin")) . "</pre></p>";
    } else if ($opt == 2) {
        $page_data['ADM_CONTENT'] .= "<div style='width:100%'>" . get_phpinfo() . "</div>";
    } else {
        $page_data['ADM_CONTENT'] = $LNG['L_GENERAL'] . ": Other opt";
        $page_data['ADM_CONTENT'] .= "Content from other opt";
    }

    return $tpl->getTPL_file("Admin", "admin_std_content", $page_data);
}

function get_phpinfo() {
    ob_start();
    phpinfo();
    $phpinfo = ob_get_contents();
    ob_end_clean();
    $phpinfo = preg_replace('/934(px)?/', '100%', $phpinfo);
    return $phpinfo;
}
