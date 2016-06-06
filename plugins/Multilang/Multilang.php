<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Multilang_init(){
    global $config;
    if (DEBUG_PLUGINS_LOAD) { print_debug("Multilang Inititated<br/>"); }

    includePluginFiles("Multilang");      
    

    register_uniq_action("get_site_langs", "ML_get_site_langs");
  
    $request_uri = $_SERVER['REQUEST_URI']; //TODO FILTER
    
    if (
       (isset($_GET['lang'])) &&
       (($lang = S_VAR_CHAR_AZ($_GET['lang'], 2, 2) ) != false)
        ) {
            $config['WEB_URL'] = $config['WEB_URL'] . "$lang/";
            $config['WEB_LANG'] = $lang;
    } else {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);   
        if(isset($lang)) {
            $config['WEB_LANG'] = $lang;
        }
    }
    
    if ($request_uri == '/') {
        $request_uri = $config['WEB_URL'] . $config['WEB_LANG'] . $request_uri;        
        header('Location:' .$request_uri);
    }

    if( //TODO: check lang agains pb_lang
        isset($_POST['choose_lang']) && (($choose_lang = S_POST_CHAR_AZ("choose_lang", 2, 2)) != false)) {
        $request_uri = str_replace("/". $config['WEB_LANG'], "/".$choose_lang, $request_uri); // FIX better method than can replace something not wanted.
        header('Location:' .$request_uri);
    }

    register_action("nav_element", "ML_nav", 6);
    addto_tplvar("SCRIPTS", ML_Script());
}

function ML_Script() {
    $script = "";
    
    if (!check_jsScript("jquery.min.js")) 
    {
        global $external_scripts;
        $external_scripts[] = "jquery.min.js";
        $script = "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\"></script>\n";
    }       
    $script = $script . "<script type='text/javascript'>\n"
            . "jQuery(function() {\n"
            . "jQuery('#choose_lang').change(function() {\n"
            . "this.form.submit();\n"
            . "});\n"
            . "});\n"
            . "</script>\n";
               
    return $script;
}
function ML_nav() { 
    global $config;

    $mlnav = "<li class='nav_right'>"
    . "<form action='#' method='post'>"
    . "<select name='choose_lang' id='choose_lang'>";
    $langs = do_action("get_site_langs");
  
    foreach ($langs as $lang) {
        if($lang['iso_code'] == $config['WEB_LANG']) {
            $mlnav .= "<option selected value='{$lang['iso_code']}'>{$lang['lang_name']}</option>";
        } else {
            $mlnav .= "<option value='{$lang['iso_code']}'>{$lang['lang_name']}</option>";
        }
    }
    $mlnav .= "</select>"
           . "</form>"
           . "</li>";
    
    return $mlnav;
}

function ML_get_langs_from_json() {
    global $config;
    
    if(file_exists($config['ML_JSON_LANGS_FILE'])) {    
        $LANGS_DATA =  json_decode(file_get_contents ($config['ML_JSON_LANGS_FILE']));
    }   
    
    return $LANGS_DATA;
}

function ML_get_site_langs() {
    global $config;    
    
    $q = "SELECT * FROM {$config['DB_PREFIX']}lang";
    $query = db_query($q);
    while($lang_row = db_fetch($query)) {
        $langs[] = array ("lang_id" => $lang_row['lang_id'],
                             "lang_name" => $lang_row['lang_name'],
                             "active" => $lang_row['active'],
                             "iso_code" => $lang_row['iso_code'],
                            );
    } 
    db_free_result($query);

    return $langs;
}

function ML_get_langs_from_db() {
    global $config;    
    
    $q = "SELECT * FROM {$config['DB_PREFIX']}lang";
    $query = db_query($q);
    while($row = db_fetch($query)) {
        $LANGS_DATA[] = (object) array ("lang_id" => $row['lang_id'],
                             "lang_name" => $row['lang_name'],
                             "active" => $row['active'],
                             "iso_code" => $row['iso_code'],
                            );
    } 
    db_free_result($query);

    return $LANGS_DATA;
}

function ML_iso_to_id($isolang) {
    
    $langs = do_action("get_site_langs");
    
    foreach ($langs as $lang) {
        if($lang['iso_code'] == $isolang) {
            return $lang['lang_id'];
        }
    }
    
    return false;
}