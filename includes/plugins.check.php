<?php
$registered_plugins = [];
$started_plugins = [];

get_all_enabled_plugins();
start_registered_plugins ();


function get_all_enabled_plugins() {
    global $registered_plugins;
    
    foreach( glob("plugins/*", GLOB_ONLYDIR)  as $plugins_dir) {
        $filename = str_replace("plugins/", "", $plugins_dir);
        $full_json_filename = "$plugins_dir/$filename.json";
        if(file_exists($full_json_filename)) {
            $jsondata = file_get_contents ("$full_json_filename");
            $plugin_data = json_decode($jsondata);
            if ($plugin_data->enabled){
                //print_debug("Plugin $plugin_data->plugin_name added to the registered<br/>");
                array_push($registered_plugins, $plugin_data);                
            } else {
                if (DEBUG_PLUGINS_LOAD) { print_debug("Plugin $plugin_data->plugin_name dropped by disable<br/>"); }
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
        if (DEBUG_PLUGINS_LOAD) { print_debug("Info: Checking $plugin->plugin_name ...<br/>"); }
        if (!$plugin->autostart) {
            if (DEBUG_PLUGINS_LOAD) { print_debug("Info: No autostart omitted<br/>"); }            
        }   else {
            if (DEBUG_PLUGINS_LOAD) { print_debug("Info: Autostart ON checking for start<br/>"); }            
            if(checker_plugin($plugin)) {                
                init_plugin($plugin);                
            }
        }
    }
    if (DEBUG_PLUGINS_LOAD) { print_debug("<br/>Info: Finish starting all register plugins with autostart ON <br/><br/>"); }            
}

function checker_plugin($plugin) {
    if (check_if_already_started($plugin)){
        if (DEBUG_PLUGINS_LOAD) { print_debug("Info: Plugin $plugin->plugin_name already started<br/>"); }
        return false;
    } else {
        if (DEBUG_PLUGINS_LOAD) { print_debug("Info: Plugin $plugin->plugin_name not started yet continue checking<br/>"); }
    }
    
    if (check_provided_conflicts($plugin)) {
        if (DEBUG_PLUGINS_LOAD) { print_debug("<b>ERROR:</b> Conflicts $plugin->plugin_name, another plugin provided *$provided*<br/>"); }
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

function plugin_manual_start($pluginname) {
    global $registered_plugins;

    if(DEBUG_PLUGINS_LOAD) { print_debug("Info: Manual order to start $pluginname<br/>"); }
    foreach ($registered_plugins as $plugin ) {
        if ($plugin->plugin_name == $pluginname) {
           if(checker_plugin($plugin)) {
                init_plugin($plugin);
                return true;
           }
        }
    }
    if (DEBUG_PLUGINS_LOAD) { print_debug("<b>Error:</b> Plugin $pluginname not exist<br/>"); }
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
        //echo "$plugin->plugin_name ($provided)<br/>";
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
    

    if (DEBUG_PLUGINS_LOAD) { print_debug("Info: All checks OK: Starting $plugin->plugin_name <br/>"); }
    require_once("plugins/$plugin->plugin_name/$plugin->main_file");
    
    
    $init_function = $plugin->function_init;
    if(function_exists($init_function)){
        $init_function();
        
    } else {
        if (DEBUG_PLUGINS_LOAD) { print_debug("<b>Error:</b>Function init on $plugin->plugin_name no exist<br>"); }
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
            if (DEBUG_PLUGINS_LOAD) { print_debug ("Info: Searching for the necessary dependencies... <br>"); }
            if( find_dependencies_and_start($depends)) {
                if (DEBUG_PLUGINS_LOAD) { print_debug ("Info: Found/Fill the necesary dependencies and we can starting the plugin <br>"); }
            } else {
                if (DEBUG_PLUGINS_LOAD) { print_debug("<b>Error</b> No depedences for this plugin in the registered plugins<br/>"); }
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
                if (DEBUG_PLUGINS_LOAD) { print_debug("Info: Plugin $plugin->plugin_name ready, has the dependence we need $depends and its already started<br/>"); }
                return true;
            }
        }
    }
    if (DEBUG_PLUGINS_LOAD) { print_debug("Info:No plugins already started to solve the dependence we need: $depends<br/>"); }
    return false;
}

function find_dependencies_and_start($depends) {
    global $registered_plugins;
    
    foreach ($registered_plugins as $plugin){
        $allprovided = preg_split('/\s+/', $plugin->provided);
        foreach ($allprovided as $provided) {
            if ($provided == $depends){
                $meet_deps = plugin_resolve_depends($plugin); //We resolv de dependes of the depends
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

