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
            if (ADMIN_DEBUG) { print_debug("ADMIN: Admin processing $plugin->plugin_name"); }
        if(!empty($plugin->function_admin_init)) {
            $admin_file = "plugins/$plugin->plugin_name/admin/$plugin->plugin_name.admin.php";
            if(file_exists($admin_file)) {
                require_once($admin_file);
                if(function_exists($plugin->function_admin_init)){
                    $init_function = $plugin->function_admin_init;
                    $init_function();                         
                }  else {
                    if(ADMIN_DEBUG) { print_debug("ADMIN: Function $plugin->function_admin_init not exist"); }
                }              
            } else {
                if(ADMIN_DEBUG) { print_debug("ADMIN: File $admin_file not exist"); }
            }
        } else {
            if(ADMIN_DEBUG) { print_debug("ADMIN: Plugin $plugin->plugin_name haven't the function admin_init declared in his json file"); }
        }            
    }
}

function Admin_GetPluginState($plugin) { 
    global $registered_plugins;
    $content = "";
    foreach ($registered_plugins as $reg_plugin) {
        if ($reg_plugin->plugin_name == $plugin) { 
            $content = getTPL_file("Admin", "plugin_state", (array) $reg_plugin);            
        }
    }    
    $content = $content . "<hr/><p><pre>" . htmlentities(Admin_GetPluginConfigFiles($plugin)) . "</pre></p>";  
    return $content;
}

function Admin_GetPluginConfigFiles($plugin) { //TODO BETTER CONFIG VIEW
    $config_plugin = "plugins/$plugin/$plugin.config.php";
    $config_plugin_user = "config/$plugin.config.php";
    $data = "";
    if (file_exists($config_plugin)) {
        $data .= file_get_contents($config_plugin);                        
    }

    if (file_exists($config_plugin_user)) { //User Overdrive
       $data .= file_get_contents($config_plugin_user);                
    }    
    
    return $data;
}