<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function admin_load_plugin_files() {
    //Load administration side from all register plugins (all enabled) and init the admin_init function.
    //TODO disable too? for enable?
    global $registered_plugins;

    foreach ($registered_plugins as $plugin) {  
            print_debug("ADMIN: Admin processing $plugin->plugin_name");
        if(!empty($plugin->function_admin_init)) {
            $admin_file = "plugins/$plugin->plugin_name/admin/$plugin->plugin_name.admin.php";
            if(file_exists($admin_file)) {
                require_once($admin_file);
                if(function_exists($plugin->function_admin_init)){
                    $init_function = $plugin->function_admin_init;
                    $init_function();                         
                }  else {
                    if('ADMIN_DEBUG') { print_debug("ADMIN: Function $plugin->function_admin_init not exist"); }
                }              
            } else {
                if('ADMIN_DEBUG') { print_debug("ADMIN: File $admin_file not exist"); }
            }
        } else {
            if('ADMIN_DEBUG') { print_debug("ADMIN: Plugin $plugin->plugin_name haven't the function admin_init declared in his json file"); }
        }            
    }
}
