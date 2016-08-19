<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }


class TPL {
    private $tpldata;
    private $scripts = [];
    private $standard_scripts = array ( //TODO LOAD LIST
        "jquery.min" => "<script  type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\" charset=\"UTF-8\"></script>\n",
    );
    private $css_cache_filepaths;
    private $css_cache_onefile;

    function build_page() {
        global $config;

        !isset($this->tpldata['ADD_TO_FOOTER']) ? $this->tpldata['ADD_TO_FOOTER'] = "" : false;
        !isset($this->tpldata['ADD_TO_BODY']) ? $this->tpldata['ADD_TO_BODY'] = "" : false;

        // BEGIN HEAD
        if($this->css_cache_check() && !empty($this->css_cache_onefile)) {
            $this->css_cache();
        }
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

        empty($filename) ? $filename = $plugin : false;
        
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

        empty($filename) ? $filename = $plugin : false;

        print_debug("Get CSS called by-> $plugin for get a $filename", "TPL_DEBUG");

        $USER_PATH = "tpl/{$config['THEME']}/css/$filename.css";
        $DEFAULT_PATH = "plugins/$plugin/tpl/css/$filename.css";
        if ($this->css_cache_check() == true) {
            if (file_exists($USER_PATH)) {
                $this->css_cache_filepaths[] = $USER_PATH;
            } else {
                $this->css_cache_filepaths[] = $DEFAULT_PATH;
            }
            if(empty($this->css_cache_onefile)) {
                $this->css_cache_onefile = $filename;
            } else {
                $this->css_cache_onefile .= "-".$filename;
            }
        } else {
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
    }

    function AddScriptFile($plugin, $filename = null, $place = "TOP" ) {
        global $config;
        print_debug("AddScriptFile request -> $plugin for get a $filename", "TPL_DEBUG");

        if( !empty($plugin) && ($plugin == "standard") ) {
            if(!$this->check_script($filename)) {
                if(array_key_exists($filename, $this->standard_scripts))  {
                    $this->addto_tplvar("SCRIPTS_".$place."", $this->standard_scripts[$filename]);
                    $this->scripts[] = $filename;
                    if(defined('TPL_DEBUG')) {
                        $backtrace = debug_backtrace();
                        print_debug("AddcriptFile:CheckScript setting first time * $filename * by ".$backtrace[1]['function']."", "TPL_DEBUG");
                    }
                } else {
                    if (defined('TPL_DEBUG')) {
                        $backtrace = debug_backtrace();
                        print_debug("AddcriptFile:CheckScript standard script * $filename * not found called by ".$backtrace[1]['function']."", "TPL_DEBUG");
                    }
                }
            } else {
                if (defined('TPL_DEBUG')) {
                    $backtrace = debug_backtrace();
                    print_debug("AddcriptFile:CheckScript found coincidence * $filename * called by ".$backtrace[1]['function']."", "TPL_DEBUG");
                }
            }
            return true;
        }

        empty($filename) ? $filename = $plugin : false;
        
        $USER_LANG_PATH = "tpl/{$config['THEME']}/js/$filename.{$config['WEB_LANG']}.js"; 
        $DEFAULT_LANG_PATH = "plugins/$plugin/js/$filename.{$config['WEB_LANG']}.js";     
        $USER_PATH = "tpl/{$config['THEME']}/js/$filename.js";
        $DEFAULT_PATH = "plugins/$plugin/js/$filename.js"; 
    
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
            $script = "<script type='text/javascript' src='/$SCRIPT_PATH' charset='UTF-8'></script>\n";
        } else {
            print_debug("AddScriptFile called by-> $plugin for get a $filename but NOT FOUND IT", "TPL_DEBUG");
            return false;
        }        
        $this->addto_tplvar("SCRIPTS_".$place."", $script);
    }

    function addto_tplvar ($tplvar, $data, $priority = 5) { // change name to appendTo_tplvar? priority support?
        //TODO add priority support
        
        if (!isset($this->tpldata[$tplvar])) {
            $this->tpldata[$tplvar] = $data;
        } else {
            $this->tpldata[$tplvar] .= $data;     
        }     
    
    }
    function addto_tplvar_uniq ($tplvar, $data) {        
        $this->tpldata[$tplvar] = $data;        
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
    
    private function check_script($script) {
        foreach ($this->scripts as $value) {
            if ($value == $script) {
                return true;
            }
        }    
        return false;
    }

    private function css_cache_check() {
        global $config;

        if ($config['CSS_OPTIMIZE'] == 0 || !is_writable("cache")) {
            return false;
        }

        if (!file_exists('cache/css')) {
            mkdir('cache/css', 0744, true);
        } else if (!is_writable('cache/css')) {
            return false;
        }
        return true;
    }
    private function css_cache() {
        $css_code = "";
        $cssfile = $this->css_cache_onefile . ".css";
        print_debug("CSS One file Unify $cssfile", "TPL_DEBUG");
        if(!file_exists("cache/css/$cssfile" )) {
            foreach ($this->css_cache_filepaths as $cssfile_path) {
                print_debug("CSS Unify  $cssfile_path","TPL_DEBUG");
                $css_code .= codetovar($cssfile_path);
            }
            $css_code = $this->css_strip($css_code);
            file_put_contents("cache/css/$cssfile", $css_code);
        }
        $this->addto_tplvar("LINK", "<link rel='stylesheet' href='/cache/css/$cssfile'>\n");
    }
    private function css_strip($css) { #by nyctimus
        $preg_replace = array(
            "#/\*.*?\*/#s" => "",  // Strip C style comments.
            "#\s\s+#"      => " ", // Strip excess whitespace.
        );
        $css = preg_replace(array_keys($preg_replace), $preg_replace, $css);
        $str_replace = array(
            ": "  => ":",
            "; "  => ";",
            " {"  => "{",
            " }"  => "}",
            ", "  => ",",
            "{ "  => "{",
            ";}"  => "}", // Strip optional semicolons.
            ",\n" => ",", // Don't wrap multiple selectors.
            "\n}" => "}", // Don't wrap closing braces.
        );
        $css = str_replace(array_keys($str_replace), $str_replace, $css);

        return trim($css);
    }
}