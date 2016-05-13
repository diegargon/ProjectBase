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
                print_debug("Plugin $plugin_data->plugin_name dropped by disable<br/>");
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
        print_debug("Checking $plugin->plugin_name ...<br/>");
        if (!$plugin->autostart) {
            print_debug("No autostart<br/>");
            
        }   else {     
            if(checker_plugin($plugin)) {
                init_plugin($plugin);
            }
        }
    }
}

function checker_plugin($plugin) {
    if (check_if_already_started($plugin)){
        print_debug("Plugin $plugin->plugin_name already started<br/>");
        return 0;
    } else {
        print_debug("Plugin $plugin->plugin_name not started yet<br/>");
    }
    
    if (check_provided_conflicts($plugin)) {
        return 0;
    }
    
    if($plugin->depends != "") {
        $meet_deps = plugin_resolve_depends($plugin);
        if ($meet_deps) {
           return 1;
        }
    } else {
       return 1;
    }    
   
    return 0;
}

function plugin_manual_start($pluginname) {
    global $registered_plugins;

    print_debug("Manual order to start $pluginname<br/>");
    foreach ($registered_plugins as $plugin ) {
        if ($plugin->plugin_name == $pluginname) {
           if(checker_plugin($plugin)) {
                init_plugin($plugin);
                return;
           }
        }
    }
    print_debug("Error:Plugin $pluginname not exist<br/>");
}

function check_if_already_started($plugin) {
    global $started_plugins;
    foreach ($started_plugins as $started_plugin){
        if ($started_plugin->plugin_name == $plugin->plugin_name){
            return 1;
        }
    }
    return 0;
}
function check_provided_conflicts ($plugin){

    
    $allprovided = preg_split('/\s+/', $plugin->provided);
    foreach ($allprovided as $provided) {
        //echo "$plugin->plugin_name ($provided)<br/>";
        if (empty($provided)) {
            return 0;
        }        
        $result = check_duplicated_provider($provided);
        if($result) {
            print_debug("ERROR: Conflicts $plugin->plugin_name, another plugin provided *$provided*<br/>");
            return 1;
        }
    }
    return 0;
}

function check_duplicated_provider($provided) {
    global $started_plugins;
    foreach ($started_plugins as $plugin) {
        $allprovided = preg_split('/\s+/', $plugin->provided);
        foreach ($allprovided as $started_provided) {        
            if ($started_provided == $provided) {                
                return 1;
            }
        }
    }
    return 0;
    
}

function init_plugin($plugin) {
    global $started_plugins;
    

    print_debug("All checks OK, starting $plugin->plugin_name <br/>"); 
    require_once("plugins/$plugin->plugin_name/$plugin->main_file");
    
    
    $init_function = $plugin->function_init;
    if(function_exists($init_function)){
        $init_function();
        
    } else {
        print_debug("<b>Error:</b>Function init on $plugin->plugin_name no exist<br>");
        return;
    }
    array_push($started_plugins, $plugin);
    
}

function plugin_resolve_depends($plugin) {
    $alldepends = preg_split('/\s+/', $plugin->depends);
    
    $meet_deps = 1;
    if ($plugin->depends == "") {
        return $meet_deps;
    }
        
    foreach ($alldepends as $depends) {
        $result = check_if_depedencie_started($depends);

        if (!$result) {
            if( find_dependencies_and_start($depends)) {
                print_debug ("Found the necesary dependence and starting it <br>");
            } else {
                print_debug("<b>Error</b> No depedences for this plugin in the registered plugins<br/>");
                $meet_deps = 0;
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
                print_debug("Plugin ready: $plugin->plugin_name has the dependence: $depends and already started<br/>");
                return 1;
            }
        }
    }
    print_debug("Info:No plugins already started solve the dependence: $depends<br/>");
    return 0;
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
                    return 1;
                } else {
                    return 0;
                }
            }
        }
    }
    return 0;
}



/*
function print_starter_register_plugins () {
    global $registered_plugins;
    global $started_plugins;
    echo "Register plugins:<br>";
    foreach ($registered_plugins as $plugin) {
        print_debug("$plugin->plugin_name<br/>");
    }
    echo "Started plugins:<br>";
    foreach ($started_plugins as $plugin) {
        print_debug("$plugin->plugin_name<br/>");
    }
    
}
*/


?>
