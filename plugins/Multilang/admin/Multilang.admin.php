<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function Multilang_AdminInit() {
    register_action("add_admin_menu", "Multilang_AdminMenu");
}

function Multilang_AdminMenu($params) {
    $tab_num = 103; //TODO: A WAY TO ASSIGN UNIQ NUMBERS
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_content", "Multilang_AdminContent");
        return "<li class='tab_active'><a href='admin&admtab=$tab_num'>Multilang</a></li>";
    } else {
        return "<li><a href='admin&admtab=$tab_num'>Multilang</a></li>";
    }
}

function Multilang_AdminContent($params) {
    global $LNG, $tpl;
    includePluginFiles("Multilang", 1);
    $tpl->getCSS_filePath("Multilang");

    $page_data['ADM_ASIDE_OPTION'] = "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=1'>" . $LNG['L_PL_STATE'] . "</a></li>\n";
    $page_data['ADM_ASIDE_OPTION'] .= "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=2'>" . $LNG['L_ML_LANGS'] . "</a></li>\n";
    $page_data['ADM_ASIDE_OPTION'] .= do_action("ADD_ADM_MULTILANG_OPT");
    $page_data['ADM_CONTENT_H1'] = "Multilang";

    if ((!$opt = S_GET_INT("opt")) || $opt == 1) {
        $page_data['ADM_CONTENT_H2'] = $LNG['L_GENERAL'] . ": " . $LNG['L_PL_STATE'];
        $page_data['ADM_CONTENT'] = Admin_GetPluginState("Multilang");
    } else if ($opt == 2) {
        !empty($_POST['btnModifyLang']) ? Multilang_ModifyLang() : false;
        !empty($_POST['btnCreateLang']) ? Multilang_CreateLang() : false;
        !empty($_POST['btnDeleteLang']) ? Multilang_DeleteLang() : false;

        $page_data['ADM_CONTENT_H2'] = $LNG['L_ML_LANGS'];
        $page_data['ADM_CONTENT'] = Multilang_AdminLangs();
    } else {
        do_action("ADM_MULTILANG_OPT", $opt);
    }
    return $tpl->getTPL_file("Admin", "admin_std_content", $page_data);
}

function Multilang_AdminLangs() {
    global $db, $tpl;
    $content = "";
    $query = $db->select_all("lang"); //change to $ml->get_site_langs(0)? but $ml class its init out of admin

    $counter = 1;
    
    $num_rows = $db->num_rows($query);
    while ($lang_row = $db->fetch($query)) {
        ($counter == $num_rows) ? $lang_row['ROW_CTR'] = 0 : $lang_row['ROW_CTR'] = $counter++;
        $content .= $tpl->getTPL_file("Multilang", "ml_admin_modify_mng", $lang_row);
    }
    $content .= $tpl->getTPL_file("Multilang", "ml_admin_create_mng");
    
    return $content;
}

function Multilang_ModifyLang() {
    global $db, $LNG, $tpl;

    $lang_id = S_POST_INT("lang_id", 11, 1);
    $lang_name = S_POST_TEXT_UTF8("lang_name", 11, 2);
    $iso_code = S_POST_CHAR_AZ("iso_code", 2, 2);
    $active = S_POST_INT("active", 1, 1);
    empty($active) ? $active = 0 : false;

    $modify_ary = [];
    $modify_ary["active"] = $active;

    if ($lang_name != false && $iso_code != false && $lang_id != false) {
        $query2 = $db->select_all("lang", [ "lang_id" => "$lang_id" ], "LIMIT 1");
        if ($db->num_rows($query2) > 0) {
            $lang_data = $db->fetch($query2);
            if ($lang_data['lang_name'] != $lang_name) {
                $query3 = $db->select_all("lang", ["lang_name" => "$lang_name" ], "LIMIT 1");
                if ($db->num_rows($query3) > 0) {
                    $tpl->addto_tplvar("ml_msg", $LNG['L_ML_WARN_FIELD_IGNORE']);
                } else {
                    $modify_ary["lang_name"] = $lang_name;
                }
            }
            if ($lang_data['iso_code'] != $iso_code) {
                $query3 = $db->select_all("lang", [ "iso_code" => "$iso_code" ], "LIMIT 1");
                if ($db->num_rows($query3) > 0) {
                    $tpl->addto_tplvar("ml_msg", $LNG['L_ML_WARN_FIELD_IGNORE']);
                } else {
                    $modify_ary["iso_code"] = $iso_code;
                }
            }
            $db->update("lang", $modify_ary, [ "lang_id" => "$lang_id" ]);
        } else {
            $tpl->addto_tplvar("ml_msg", $LNG['L_ML_E_INTERNAL_ID']);
        }
    } else {
        $tpl->addto_tplvar("ml_msg", $LNG['L_ML_E_FIELDS']);
    }
}

function Multilang_CreateLang() {
    global $db, $LNG, $tpl;

    $lang_name = S_POST_TEXT_UTF8("lang_name", 11, 2);
    $iso_code = S_POST_CHAR_AZ("iso_code", 2, 2);
    $active = S_POST_INT("active", 1, 1);
    empty($active) ? $active = 0 : false;

    if ($lang_name != false && $iso_code != false) {

        //Lang/ISo collation its utf8_general_ci (case insensitve), anyway we use LIKE operator instead '=' 
        $where_ary = [
            "lang_name" => [ "value" => "$lang_name", "operator" => "LIKE"],
            "iso_code" => [ "value" => "$iso_code", "operator" => "LIKE"]
        ];
        $query = $db->select_all("lang", $where_ary, "LIMIT 1", "OR");

        if ($db->num_rows($query) == 0) {
            $db->insert("lang", ["lang_name" => "$lang_name", "active" => "$active", "iso_code" => "$iso_code"]);
            $tpl->addto_tplvar("ml_msg", $LNG['L_ML_CREATE_SUCCESFUL']);
        } else {
            $tpl->addto_tplvar("ml_msg", $LNG['L_ML_E_FIELDS_EXISTS']);
        }
    } else {
        $tpl->addto_tplvar("ml_msg", $LNG['L_ML_E_FIELDS']);
    }
}

function Multilang_DeleteLang() {
    global $db, $LNG, $tpl;

    $lid = S_POST_INT("lang_id", 11);
    if ($lid != false) {
        $db->delete("lang", [ "lang_id" => "$lid" ]);
        $tpl->addto_tplvar("ml_msg", $LNG['L_ML_DELETE_SUCCESS']);
    } else {
        $tpl->addto_tplvar("ml_msg", $LNG['L_ML_E_INTERNAL_ID']);
    }
}
