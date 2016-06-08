<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

class Multilang {
    private $active_site_langs;
    private $site_langs;
            
    function __construct() {
        $this->get_site_langs(1);
   }
    

    function get_js() {
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

    function get_nav() { 
        global $config;

        $mlnav = "<li class='nav_right'>"
            . "<form action='#' method='post'>"
            . "<select name='choose_lang' id='choose_lang'>";
  
        foreach ($this->get_site_langs(1) as $lang) {
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
    
    function get_site_langs($active) {
        if (empty($this->site_langs) && empty($active)) {
          $this->retrieve_db_langs();            
        }
        if (empty($this->active_site_langs) && !empty($active)) {
          $this->retrieve_db_langs(1);            
        }        
                
        if ($active) {
            return $this->active_site_langs;
        } else {
            return $this->site_langs;
        }
    }

    function iso_to_id($isolang) {    
        
        foreach ($this->get_site_langs() as $lang) {
            if($lang['iso_code'] == $isolang) {
                return $lang['lang_id'];
            }
        }    
    return false;
    }    
    
    private function retrieve_db_langs($active= null) {
        global $config, $db;

        if (!empty($active)) {
            $query = $db->select_all("lang", array("active" => "$active"));
        } else{
            $query = $db->select_all("lang");
        }
        while($lang_row = $db->fetch($query)) {
            if ($active) { //TODO better
                $this->active_site_langs[] = array ("lang_id" => $lang_row['lang_id'],
                             "lang_name" => $lang_row['lang_name'],
                             "active" => $lang_row['active'],
                             "iso_code" => $lang_row['iso_code'],
                            );
            } else {
                $this->site_langs[] = array ("lang_id" => $lang_row['lang_id'],
                             "lang_name" => $lang_row['lang_name'],
                             "active" => $lang_row['active'],
                             "iso_code" => $lang_row['iso_code'],
                            );
                
            }
        }

        $db->free($query);
    }
}