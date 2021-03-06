<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

class Multilang {
    private $active_site_langs;
    private $site_langs;
            
    function __construct() {
        $this->get_site_langs();
    }

    function getSessionLang() {
        global $cfg;
        $lid = $this->iso_to_id($cfg['WEB_LANG']);        
        return $this->active_site_langs[$lid];
    }
    function getSessionLangId() {
        global $cfg;
        return $this->iso_to_id($cfg['WEB_LANG']);
    }    
    function get_nav() { 
        global $cfg;

        $mlnav = "<li class='nav_right'>"
            . "<form action='#' method='post'>"            
            . "<select name='choose_lang' id='choose_lang' onchange=\"this.form.submit()\" >";
        
        foreach ($this->get_site_langs() as $lang) {
            if($lang['iso_code'] == $cfg['WEB_LANG']) {
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

    function get_site_langs($active = 1) {
        if (empty($this->site_langs) && empty($active)) {
          $this->retrieve_db_langs();            
        }
        if (empty($this->active_site_langs) && !empty($active)) {
          $this->retrieve_db_langs(1);            
        }        

        return ($active) ? $this->active_site_langs : $this->site_langs;
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
        global $db;

        if (!empty($active)) {
            $query = $db->select_all("lang", [ "active" => "$active"]);
        } else{
            $query = $db->select_all("lang");
        }
        while($lang_row = $db->fetch($query)) {
            if ($active) { 
                $this->active_site_langs[$lang_row['lang_id']] = $lang_row;
            } else {
                $this->site_langs[$lang_row['lang_id']] = $lang_row;
            }
        }
        $db->free($query);
    }
}