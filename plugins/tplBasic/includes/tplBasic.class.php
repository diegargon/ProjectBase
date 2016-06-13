<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }


class TPL {
    private $tpldata;
    
    function build_page() {
        global $config;
    
        !isset($this->tpldata['ADD_TO_FOOTER']) ? $this->tpldata['ADD_TO_FOOTER'] = "" : false;
        !isset($this->tpldata['ADD_TO_BODY']) ? $this->tpldata['ADD_TO_BODY'] = "" : false;
    
        // BEGIN HEAD
 
        $web_head = do_action("get_head"); 
        //END HEAD
    
        //BEGIN BODY
        if ($config['NAV_MENU']) { //we use do_action for select order
            !isset($this->tpldata['NAV_ELEMENT']) ? $this->tpldata['NAV_ELEMENT'] = "" : false;            
            $this->tpldata['NAV_ELEMENT'] .= do_action("nav_element");       
        }   
    
        $this->tpldata['ADD_TO_BODY'] .= do_action("add_to_body");
        $web_body = do_action("get_body");
        //END BODY
    
        //BEGIN FOOTER 
        $this->tpldata['ADD_TO_FOOTER'] .= do_action("add_to_footer");
        $web_footer = do_action("get_footer");
        //END FOOTER
        
        echo $web_head . $web_body . $web_footer;
    }

    function getTPL_file($plugin, $filename = null, $data = null) {
        global $config;

        if(empty($filename)) {
            $filename = $plugin;
        }        
        print_debug("getTPL_file called by-> $plugin for get a $filename", "TPL_DEBUG");

        $USER_PATH = "tpl/{$config['THEME']}/$filename.tpl.php";
        $DEFAULT_PATH = "plugins/$plugin/tpl/$filename.tpl.php";
        if (file_exists($USER_PATH)) {
            $tpl_file_content = codetovar($USER_PATH, $data);
        } else if (file_exists($DEFAULT_PATH)) {
            $tpl_file_content = codetovar($DEFAULT_PATH, $data);
        } else {
            print_debug("getTPL_file called but not find $filename", "TPL_DEBUG");
            return false;
        }
    
        return $tpl_file_content;  
    }

    function getCSS_filePath($plugin, $filename = null) {
        global $config;
    
        if(empty($filename)) {
            $filename = $plugin;
        }    
        print_debug("Get CSS called by-> $plugin for get a $filename", "TPL_DEBUG");

        $USER_PATH = "tpl/{$config['THEME']}/css/$filename.css";
        $DEFAULT_PATH = "plugins/$plugin/tpl/css/$filename.css";        
        if (file_exists($USER_PATH))  {
            $css = "<link rel='stylesheet' href='/$USER_PATH'>\n";
        } else if (file_exists($DEFAULT_PATH)) {
            $css =  "<link rel='stylesheet' href='/$DEFAULT_PATH'>\n";           
        }
        if (isset($css)) {
            $this->addto_tplvar("LINK", $css);
        } else {
            print_debug("Get CSS called by-> $plugin for get a $filename NOT FOUND IT", "TPL_DEBUG");
        }
    }

    function getScript_fileCode($plugin, $filename = null) {
        global $config;
    
        if(empty($filename)) {
            $filename = $plugin;
        }    
        print_debug("Get Script called by-> $plugin for get a $filename", "TPL_DEBUG");

        $USER_LANG_PATH = "tpl/{$config['THEME']}/js/$filename.{$config['WEB_LANG']}.js"; 
        $DEFAULT_LANG_PATH = "plugins/$plugin/tpl/js/$filename.{$config['WEB_LANG']}.js";     
        $USER_PATH = "tpl/{$config['THEME']}/js/$filename.js";
        $DEFAULT_PATH = "plugins/$plugin/tpl/js/$filename.js"; 
    
        if (file_exists($USER_LANG_PATH))  { //TODO Recheck priority later
            $SCRIPT_PATH = $USER_LANG_PATH;
        } else if (file_exists($USER_PATH)) {
            $SCRIPT_PATH = $USER_PATH;
        } else if (file_exists($DEFAULT_LANG_PATH))  {
            $SCRIPT_PATH = $DEFAULT_LANG_PATH;
        } else if (file_exists($DEFAULT_PATH)) {
            $SCRIPT_PATH = $DEFAULT_PATH;
        }
        if (!empty($SCRIPT_PATH)) {
            return  "<script type='text/javascript' src='$SCRIPT_PATH'></script>\n";
        } else {
            print_debug("Get Script called by-> $plugin for get a $filename but NOT FOUND IT", "TPL_DEBUG");
            return false;
        }
    }

    function addto_tplvar ($tplvar, $data, $priority = 5) { // change name to appendTo_tplvar? priority support?
        //TODO add priority support
        
        if (!isset($this->tpldata[$tplvar])) {
            $this->tpldata[$tplvar] = $data;
        } else {
            $this->tpldata[$tplvar] .= $data;     
        }     
    
    }  
    function add_if_empty($tplvar, $data) {

        if(empty($this->tpldata[$tplvar])) {
            $this->tpldata[$tplvar] = $data;
        }
    }
    
    function addtpl_array($tpl_ary) {
        foreach ($tpl_ary as $key => $value) {
            $this->addto_tplvar($key, $value);
        }
    }
    
    function gettpl_value($value) {
        return $this->tpldata[$value];
    }
    function get_tpldata() {
        return $this->tpldata;
    }
}

