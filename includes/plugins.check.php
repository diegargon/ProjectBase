<?php
if (!defined('IN_WEB')) { exit; }

function get_all_enabled_plugins() {
    global $registered_plugins;
    
    foreach( glob("plugins/*", GLOB_ONLYDIR)  as $plugins_dir) {
        $filename = str_replace("plugins/", "", $plugins_dir);
        $full_json_filename = "$plugins_dir/$filename.json";
        if(file_exists($full_json_filename)) {
            $jsondata = file_get_contents ("$full_json_filename");
            $plugin_data = json_decode($jsondata);
            if ($plugin_data->enabled){
                print_debug("Plugin $plugin_data->plugin_name added to the registered", "PLUGIN_LOAD");
                array_push($registered_plugins, $plugin_data);                
            } else {
                print_debug("Plugin $plugin_data->plugin_name dropped by disable", "PLUGIN_LOAD"); 
            }
        }
    }
}

function start_registered_plugins() {
    global $registered_plugins;
    
    usort($registered_plugins, function($a, $b) {
        return $a->priority - $b->priority;
    });

    foreach ($registered_plugins as $plugin ) {
        print_debug("Info: Checking $plugin->plugin_name ...", "PLUGIN_LOAD");
        if (!$plugin->autostart) {
            print_debug("Info: No autostart omitted", "PLUGIN_LOAD");         
        }   else {
            print_debug("Info: Autostart ON checking for start", "PLUGIN_LOAD");            
            if(checker_plugin($plugin)) {                
                init_plugin($plugin);                
            }
        }
    }
    print_debug("Info: Finish starting all register plugins with autostart ON ", "PLUGIN_LOAD");             
}

function checker_plugin($plugin) {
    if (check_if_already_started($plugin)){
        print_debug("Info: Plugin $plugin->plugin_name already started", "PLUGIN_LOAD");
        return false;
    } else {
        print_debug("Info: Plugin $plugin->plugin_name not started yet continue checking", "PLUGIN_LOAD");
    }
    
    if (check_provided_conflicts($plugin)) {
        print_debug("<b>ERROR:</b> Conflicts $plugin->plugin_name, another plugin provided", "PLUGIN_LOAD");
        return false;
    }
    
    if($plugin->depends != "") {
        $meet_deps = plugin_resolve_depends($plugin);
        if ($meet_deps) {
           return true;
        }
    } else {
       return true;
    }    
   
    return false;
}

function plugin_start($pluginname) {
    global $registered_plugins;    
    
    print_debug("Info: order to start $pluginname", "PLUGIN_LOAD");
    
    foreach ($registered_plugins as $plugin ) {
        if ($plugin->plugin_name == $pluginname) {
           if(checker_plugin($plugin)) {
                init_plugin($plugin);
                return true;
           }
        }
    }
    print_debug("<b>Error:</b> Plugin $pluginname not exist", "PLUGIN_LOAD");
    
    return false;        
}
function check_if_already_started($plugin) {
    global $started_plugins;
    foreach ($started_plugins as $started_plugin){
        if ($started_plugin->plugin_name == $plugin->plugin_name){
            return true;
        }
    }
    return false;
}
function check_provided_conflicts ($plugin){ 
    $allprovided = preg_split('/\s+/', $plugin->provided);
    foreach ($allprovided as $provided) {        
        if (empty($provided)) {
            return false;
        }        
        $result = check_duplicated_provider($provided);
        if($result) {            
            return true;
        }
    }
    return false;
}

function check_duplicated_provider($provided) {
    global $started_plugins;
    foreach ($started_plugins as $plugin) {
        $allprovided = preg_split('/\s+/', $plugin->provided);
        foreach ($allprovided as $started_provided) {        
            if ($started_provided == $provided) {                
                return true;
            }
        }
    }
    return false;    
}

function init_plugin($plugin) {
    global $started_plugins;
    
    print_debug("Info: All checks OK: Starting $plugin->plugin_name ", "PLUGIN_LOAD"); 
    require_once("plugins/$plugin->plugin_name/$plugin->main_file");
        
    $init_function = $plugin->function_init;
    if(function_exists($init_function)){
        $init_function();        
    } else {
        print_debug("<b>Error:</b>Function init on $plugin->plugin_name no exist", "PLUGIN_LOAD");
        return false;
    }
    array_push($started_plugins, $plugin);
    return true;
}

function plugin_resolve_depends($plugin) {
    $alldepends = preg_split('/\s+/', $plugin->depends);    
    $meet_deps = true;
    if ($plugin->depends == "") {
        return $meet_deps;
    }
        
    foreach ($alldepends as $depends) {
        $result = check_if_depedencie_started($depends);

        if (!$result) {
            print_debug ("Info: Searching for the necessary dependencies... ", "PLUGIN_LOAD"); 
            if( find_dependencies_and_start($depends)) {
                print_debug ("Info: Found/Fill the necesary dependencies and we can starting the plugin ", "PLUGIN_LOAD"); 
            } else {
                print_debug("<b>Error</b> No depedences for this plugin in the registered plugins", "PLUGIN_LOAD"); 
                $meet_deps = false;
            }
        }
    }
    
    return $meet_deps;
}


function check_if_depedencie_started($depends) {
    global $started_plugins;
    
    foreach ($started_plugins as $plugin) {
        $allprovided = preg_split('/\s+/', $plugin->provided);
        foreach ($allprovided as $provided) {        
            if ($provided == $depends) {
                print_debug("Info: Plugin $plugin->plugin_name ready, has the dependence we need $depends and its already started", "PLUGIN_LOAD"); 
                return true;
            }
        }
    }
    print_debug("Info:No plugins already started to solve the dependence we need: $depends", "PLUGIN_LOAD"); 
    
    return false;
}

function find_dependencies_and_start($depends) {
    global $registered_plugins;
    
    foreach ($registered_plugins as $plugin){
        $allprovided = preg_split('/\s+/', $plugin->provided);
        foreach ($allprovided as $provided) {
            if ($provided == $depends){
                $meet_deps = plugin_resolve_depends($plugin); //resolv de dependes of the depends
                if ($meet_deps) {
                    init_plugin($plugin);
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
    return false;
}

function plugin_check_enable($plugin) {
    global $registered_plugins;  
    
    foreach ($registered_plugins as $reg_plugin) {
        if ($reg_plugin['plugin_name'] == $plugin) {
            return true;
        }
    }
    return false;
}