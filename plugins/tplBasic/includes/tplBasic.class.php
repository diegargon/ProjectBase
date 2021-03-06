<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

class TPL {

    private $cfg;
    private $db;
    private $tpldata = null;
    private $scripts = [];
    private $std_remote_scripts = array(//TODO LOAD LIST
        "jquery" => "https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js",
        "font-awesome" => "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css",
        "bootstrap" => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css",
        "angular" => "https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular.min.js",
        "dogo" => "https://ajax.googleapis.com/ajax/libs/dojo/1.11.2/dojo/dojo.js",
        "ext-core" => "https://ajax.googleapis.com/ajax/libs/ext-core/3.1.0/ext-core.js",
        "hammer" => "https://ajax.googleapis.com/ajax/libs/hammerjs/2.0.8/hammer.min.js",
        "mootools" => "https://ajax.googleapis.com/ajax/libs/mootools/1.6.0/mootools.min.js",
        "prototype" => "https://ajax.googleapis.com/ajax/libs/prototype/1.7.3.0/prototype.js",
        "scriptaculous" => "https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/scriptaculous.js",
        "spf" => "https://ajax.googleapis.com/ajax/libs/spf/2.4.0/spf.js",
        "swfobject" => "https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js",
        "three" => "https://ajax.googleapis.com/ajax/libs/threejs/r76/three.min.js",
        "webfont" => "https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js",
    );
    private $css_cache_filepaths;
    private $css_cache_onefile;

    function __construct($cfg, $db = null) {
        $this->cfg = $cfg;
        $this->db = $db;
    }

    function build_page() {

        !isset($this->tpldata['ADD_TO_FOOTER']) ? $this->tpldata['ADD_TO_FOOTER'] = "" : null;
        !isset($this->tpldata['ADD_TO_BODY']) ? $this->tpldata['ADD_TO_BODY'] = "" : null;

        // BEGIN HEAD
        $this->css_cache_check() && !empty($this->css_cache_onefile) ? $this->css_cache() : null;
        $web_head = do_action("get_head");
        //END HEAD
        //BEGIN BODY
        if ($this->cfg['NAV_MENU']) { //we use do_action for select order
            !isset($this->tpldata['HEADER_MENU_ELEMENT']) ? $this->tpldata['HEADER_MENU_ELEMENT'] = "" : null;
            $this->tpldata['HEADER_MENU_ELEMENT'] .= do_action("header_menu_element");
        }

        $this->tpldata['ADD_TO_BODY'] .= do_action("add_to_body");
        $web_body = do_action("get_body");
        //END BODY
        //BEGIN FOOTER
        if (defined('SQL') && $this->db != null && $this->cfg['STATS_QUERYS']) {
            $this->tpldata['ADD_TO_FOOTER'] .= "<p class='center zero'>Querys(" . $this->db->num_querys() . ")</p>";
        }
        $this->tpldata['ADD_TO_FOOTER'] .= do_action("add_to_footer");

        $web_footer = do_action("get_footer");
        //END FOOTER

        echo $web_head . $web_body . $web_footer;
    }

    function getTPL_file($plugin, $filename = null, $data = null) {

        empty($filename) ? $filename = $plugin : null;

        print_debug("getTPL_file called by-> $plugin for get a $filename", "TPL_DEBUG");

        $USER_PATH_LANG = "tpl/{$this->cfg['THEME']}/$filename.{$this->cfg['WEB_LANG']}.tpl.php";
        $USER_PATH = "tpl/{$this->cfg['THEME']}/$filename.tpl.php";
        $DEFAULT_PATH = "plugins/$plugin/tpl/$filename.tpl.php";
        if (file_exists($USER_PATH_LANG)) {
            $tpl_file_content = codetovar($USER_PATH_LANG, $data);
        } else if (file_exists($USER_PATH)) {
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

        empty($filename) ? $filename = $plugin : null;

        print_debug("Get CSS called by-> $plugin for get a $filename", "TPL_DEBUG");

        $USER_PATH = "tpl/{$this->cfg['THEME']}/css/$filename.css";
        $DEFAULT_PATH = "plugins/$plugin/tpl/css/$filename.css";
        if ($this->css_cache_check() == true) {
            if (file_exists($USER_PATH)) {
                $this->css_cache_filepaths[] = $USER_PATH;
            } else {
                $this->css_cache_filepaths[] = $DEFAULT_PATH;
            }
            if (empty($this->css_cache_onefile)) {
                $this->css_cache_onefile = $filename;
            } else {
                $this->css_cache_onefile .= "-" . $filename;
            }
        } else {
            if ($this->cfg['CSS_INLINE'] == 0) {
                if (file_exists($USER_PATH)) {
                    $css = "<link rel='stylesheet' href='/$USER_PATH'>\n";
                } else if (file_exists($DEFAULT_PATH)) {
                    $css = "<link rel='stylesheet' href='/$DEFAULT_PATH'>\n";
                }
            } else {
                if (file_exists($USER_PATH)) {
                    $css_code = codetovar("$USER_PATH");
                } else if (file_exists($DEFAULT_PATH)) {
                    $css_code = codetovar("$DEFAULT_PATH");
                }
                isset($css_code) ? $css = "<style>" . $this->css_strip($css_code) . "</style>" : null;
            }
            if (isset($css)) {
                $this->addto_tplvar("LINK", $css);
            } else {
                print_debug("Get CSS called by-> $plugin for get a $filename NOT FOUND IT", "TPL_DEBUG");
            }
        }
    }

    function AddScriptFile($plugin, $filename = null, $place = "TOP", $async = "async") {

        print_debug("AddScriptFile request -> $plugin for get a $filename", "TPL_DEBUG");

        if (!empty($plugin) && ($plugin == "standard")) {
            if (!$this->check_script($filename)) {
                if (array_key_exists($filename, $this->std_remote_scripts)) {
                    $script_url = $this->std_remote_scripts[$filename];
                    $script = "<script type='text/javascript' src='$script_url' charset='UTF-8' $async></script>\n";
                    $this->addto_tplvar("SCRIPTS_" . $place . "", $script);
                    $this->scripts[] = $filename;
                    if (defined('TPL_DEBUG')) {
                        $backtrace = debug_backtrace();
                        print_debug("AddcriptFile:CheckScript setting first time * $filename * by " . $backtrace[1]['function'] . "", "TPL_DEBUG");
                    }
                } else {
                    if (defined('TPL_DEBUG')) {
                        $backtrace = debug_backtrace();
                        print_debug("AddcriptFile:CheckScript standard script * $filename * not found called by " . $backtrace[1]['function'] . "", "TPL_DEBUG");
                    }
                }
            } else {
                if (defined('TPL_DEBUG')) {
                    $backtrace = debug_backtrace();
                    print_debug("AddcriptFile:CheckScript found coincidence * $filename * called by " . $backtrace[1]['function'] . "", "TPL_DEBUG");
                }
            }
            return true;
        }

        empty($filename) ? $filename = $plugin : null;

        $USER_LANG_PATH = "tpl/{$this->cfg['THEME']}/js/$filename.{$this->cfg['WEB_LANG']}.js";
        $DEFAULT_LANG_PATH = "plugins/$plugin/js/$filename.{$this->cfg['WEB_LANG']}.js";
        $USER_PATH = "tpl/{$this->cfg['THEME']}/js/$filename.js";
        $DEFAULT_PATH = "plugins/$plugin/js/$filename.js";

        if (file_exists($USER_LANG_PATH)) { //TODO Recheck priority later
            $SCRIPT_PATH = $USER_LANG_PATH;
        } else if (file_exists($USER_PATH)) {
            $SCRIPT_PATH = $USER_PATH;
        } else if (file_exists($DEFAULT_LANG_PATH)) {
            $SCRIPT_PATH = $DEFAULT_LANG_PATH;
        } else if (file_exists($DEFAULT_PATH)) {
            $SCRIPT_PATH = $DEFAULT_PATH;
        }
        if (!empty($SCRIPT_PATH)) {
            $script = "<script type='text/javascript' src='{$this->cfg['STATIC_SRV_URL']}$SCRIPT_PATH' charset='UTF-8' $async></script>\n";
        } else {
            print_debug("AddScriptFile called by-> $plugin for get a $filename but NOT FOUND IT", "TPL_DEBUG");
            return false;
        }
        $this->addto_tplvar("SCRIPTS_" . $place . "", $script);
    }

    function addto_tplvar($tplvar, $data, $priority = 5) { // change name to appendTo_tplvar? TODO priority support?
        !isset($this->tpldata[$tplvar]) ? $this->tpldata[$tplvar] = $data : $this->tpldata[$tplvar] .= $data;      
    }

    function addto_tplvar_uniq($tplvar, $data) {
        $this->tpldata[$tplvar] = $data;
    }

    function add_if_empty($tplvar, $data) {
        empty($this->tpldata[$tplvar]) ? $this->tpldata[$tplvar] = $data : null;
    }

    function addtpl_array($tpl_ary) {
        if (empty($tpl_ary)) {
            return false;
        }
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

    function addStdScript($key, $url) {
        if (array_key_exists($key, $this->std_remote_scripts)) {
            return 0;
        }
        $this->std_remote_scripts[$key] = $url;
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
        if ($this->cfg['CSS_OPTIMIZE'] == 0 || !is_writable("cache")) {
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
        if (!file_exists("cache/css/$cssfile")) {
            foreach ($this->css_cache_filepaths as $cssfile_path) {
                print_debug("CSS Unify  $cssfile_path", "TPL_DEBUG");
                $css_code .= codetovar($cssfile_path);
            }
            $css_code = $this->css_strip($css_code);
            file_put_contents("cache/css/$cssfile", $css_code);
        }
        if ($this->cfg['CSS_INLINE'] == 0) {
            $this->addto_tplvar("LINK", "<link rel='stylesheet' href='/cache/css/$cssfile'>\n");
        } else {
            $css_code = codetovar("cache/css/$cssfile");
            $this->addto_tplvar("LINK", "<style>$css_code</style>\n");
        }
    }

    private function css_strip($css) { #by nyctimus
        $preg_replace = array(
            "#/\*.*?\*/#s" => "", // Strip C style comments.
            "#\s\s+#" => " ", // Strip excess whitespace.
        );
        $css = preg_replace(array_keys($preg_replace), $preg_replace, $css);
        $str_replace = array(
            ": " => ":",
            "; " => ";",
            " {" => "{",
            " }" => "}",
            ", " => ",",
            "{ " => "{",
            ";}" => "}", // Strip optional semicolons.
            ",\n" => ",", // Don't wrap multiple selectors.
            "\n}" => "}", // Don't wrap closing braces.
        );
        $css = str_replace(array_keys($str_replace), $str_replace, $css);

        return trim($css);
    }

}
