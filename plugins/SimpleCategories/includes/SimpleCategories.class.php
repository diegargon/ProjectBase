<?php

/*
 *  Copyright @ 2016 Diego Garcia
 * 
 *  Class for determine name with "name" check parent for avoid error with same name in
 *  diferent subcategories, not fully tested and going to have problems if we have 
 *  something like...
 *  /News/Sports/Football and /Other/Sports/Football  ( That going to fail, first match return)
 *  /News/Sports/Football and /News/Videos/Football (its ok)
 * 
 */
!defined('IN_WEB') ? exit : true;

class Categories {

    private $categories = [];

    public function __construct() {
        $this->loadCategories();
    }

    function DebugCats() {
        print_r($this->categories);
    }

    function getCatbyName($plugin, $catname, $father = 0) {
        if (empty($plugin) || empty($catname)) {
            return false;
        }

        foreach ($this->categories as $key => $category) {
            if (array_search($plugin, $category)) {
                if (array_search($catname, $category)) {
                    if (!empty($father)) {
                        $f_id = $category['father'];
                        if ($this->categories[$f_id]['name'] == $father) {
                            return $this->categories[$key];
                        }
                    } else {
                        if (array_search($father, $category)) {
                            return $this->categories[$key];
                        }
                    }
                }
            }
        }
    }

    function getCatIDbyName($plugin, $catname, $father = 0) {
        if (empty($plugin) || empty($catname)) {
            return false;
        }
        $category = $this->getCatbyName($plugin, $catname, $father);

        return $category['cid'];
    }

    function getCatIDbyName_path($plugin, $cat_path, $separator = ".") {
        if (empty($plugin) || empty($cat_path)) {
            return false;
        }
        $cat_path_ary = explode($separator, $cat_path);
        if (count($cat_path_ary) > 1) {
            $catname = array_pop($cat_path_ary);
            $catparent = array_pop($cat_path_ary);
            $cat_id = $this->getCatIDbyName($plugin, $catname, $catparent);
        } else {
            $catname = array_pop($cat_path_ary);
            $cat_id = $this->getCatIDbyName($plugin, $catname);
        }

        return $cat_id;
    }

    function getCatChildsId($plugin, $cats, $separator = ",") {
        $cat_ids = "";

        if (empty($plugin) || empty($cats)) {
            return false;
        }
        $cats = ltrim($cats, $separator); //remove first ',' if we have(in loop)

        $cats_ids_ary = explode($separator, $cats);

        foreach ($cats_ids_ary as $cat_id) {
            foreach ($this->categories as $category) {
                if ($category['plugin'] == $plugin && $category['father'] == $cat_id) {
                    $cat_ids .= $separator . $category['cid'];
                }
            }
        }
        //loop
        !empty($cat_ids) ? $cat_ids .= $this->getCatChildsId($plugin, $cat_ids) : false;

        return $cat_ids;
    }

    function root_cats($plugin, $formated = 1) { // get_fathers_cat_list
        global $config, $LANGDATA;

        if (empty($plugin)) {
            return false;
        }

        $cat_data = "";

        foreach ($this->categories as $category) {
            if ($category['plugin'] == $plugin && $category['father'] == 0) {
                if ($formated) {
                    $cat_display_name = preg_replace('/\_/', ' ', $category['name']);
                    $cat_data .= "<li><a href='/{$config['WEB_LANG']}/{$LANGDATA['L_NEWS_SECTION']}/{$category['name']}'>$cat_display_name</a></li>";
                } else {
                    $cat_data[$category['cid']] = $category;
                }
            }
        }

        return $cat_data;
    }

    function childs_of_cat($plugin, $cat_path, $formated = 1, $separator = ".") { //FORREMOVE
        global $config, $LANGDATA;

        if (empty($plugin) && empty($cat_path)) {
            return false;
        }
        $cats_explode = explode($separator, $cat_path);
        $cat_data = "";

        $cat_id = $this->getCatIDbyName_path($plugin, $cat_path);

        if ($formated && count($cats_explode) > 1) {
            array_pop($cats_explode);
            $f_cats = implode($separator, $cats_explode);
            $cat_data .= "<li><a href='/{$config['WEB_LANG']}/{$LANGDATA['L_NEWS_SECTION']}/$f_cats'>{$config['CATS_BACK_SYMBOL']}</a></li>";
        }
        foreach ($this->categories as $category) {
            if ($category['plugin'] == $plugin && $category['father'] == $cat_id) {
                if ($formated) {
                    $cat_display_name = preg_replace('/\_/', ' ', $category['name']);
                    $cat_data .= "<li><a href='/{$config['WEB_LANG']}/{$LANGDATA['L_NEWS_SECTION']}/$cat_path.{$category['name']}'>$cat_display_name</a></li>";
                } else {
                    $cat_data = $category;
                }
            }
        }

        return !empty($cat_data) ? $cat_data : false;
    }

    function sortCatsByWeight() {
        usort($this->categories, function($a, $b) {
            return $a['weight'] - $b['weight'];
        });
    }

    function sortCatsByViews() {
        usort($this->categories, function($a, $b) {
            return $a['views'] - $b['views'];
        });
    }

    private function loadCategories($plugin = null) {
        global $db, $ml, $config;
        $where_ary = [];

        $plugin = $config['CATS_DEFAULT_LOAD_PLUGIN'];

        defined('MULTILANG') ? $lang_id = $ml->getSessionLangId() : $lang_id = $config['WEB_LANG_ID'];

        if (!empty($lang_id) && is_numeric($lang_id)) {
            $where_ary['lang_id'] = $lang_id;
        }
        $config['CATS_BY_VIEWS'] ? $order = "views DESC" : $order = "weight ASC";

        $plugin ? $where_ary['plugin'] = $plugin : null;
        $query = $db->select_all("categories", $where_ary, "ORDER BY $order");
        while ($c_row = $db->fetch($query)) {
            $this->categories[$c_row['cid']] = $c_row;
        }
    }

}
