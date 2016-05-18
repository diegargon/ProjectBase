<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */


function Multilang_init(){
    global $config;
    print_debug("Multilang Inititated<br/>");
    include_once("Multilang.config.php");
    
    $config['multilang'] = 1;
    
    //MUST PRIOTIZE MYSQL Plugin load before starting multilang for use from_db
    if (isset($config['SQL_DB']) && $config['SQL_DB'] == 1 && $config['ML_USE_JSON_LANGS'] == 0) {
        register_uniq_action("get_site_langs", "ML_get_langs_from_db");
    } else {
        register_uniq_action("get_site_langs", "ML_get_langs_from_json");
    }
    
    $request_uri = $_SERVER['REQUEST_URI'];

    if (
       (isset($_GET['lang'])) &&
       (($lang = s_char($_GET['lang'], 2)) != false)
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
            isset($_POST['choose_lang']) &&
            (($choose_lang = s_char($_POST['choose_lang'], 3)) != false)
            ) {
            $request_uri = str_replace($config['WEB_LANG'], $choose_lang, $request_uri);
            header('Location:' .$request_uri);
    }

    register_action("add_nav_element", "ML_nav", 6);
    register_action("add_script", "ML_Script", 5);   
}

function ML_Script() {
    //TODO: Plugin for provided common scripts "need_jquery() and asured its included only one time if its called from other modules";
    //TODO: jquery ATM its duplicated on login.
    $script = "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\"></script>\n";
    $script .= "<script type='text/javascript'>\n"
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
    . "<form action='' method='post'>"
    . "<select name='choose_lang' id='choose_lang'>";

    $LANGS = do_action("get_site_langs");

    
    foreach ($LANGS as $content) {
        if($content->iso_code == $config['WEB_LANG']) {
            $mlnav .= "<option selected value='$content->iso_code'>$content->lang_name</option>";
        } else {
            $mlnav .= "<option value='$content->iso_code'>$content->lang_name</option>";
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
