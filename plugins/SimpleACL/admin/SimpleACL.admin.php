<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SimpleACL_AdminInit() {
    register_action("add_admin_menu", "SimpleACL_AdminMenu", 5); 
}

function SimpleACL_AdminMenu($params) {
    //TODO A way to assign uniq numbers
    $tab_num = 102;
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_content", "SimpleACL_AdminContent");        
        return "<li class='tab_active'><a href='?admtab=$tab_num'>SimpleACL</a></li>";
    } else {
        return "<li><a href='?admtab=$tab_num'>SimpleACL</a></li>";
    }
}

function SimpleACL_AdminContent($params) {
    global $tpl, $LANGDATA;    
    
    $msg = "";
    
    $tpl->getCSS_filePath("SimpleACL");
    
    $tpl->addto_tplvar("ADM_ASIDE_OPTION", "<li><a href='?admtab=" . $params['admtab'] ."&opt=1'>". $LANGDATA['L_PL_STATE'] ."</a></li>\n" );
    $tpl->addto_tplvar("ADM_ASIDE_OPTION", "<li><a href='?admtab=" . $params['admtab'] ."&opt=2'>". $LANGDATA['L_ACL_ROLES'] ."</a></li>\n");
    //$tpl->addto_tplvar("ADM_ASIDE_OPTION", "<li><a href='?admtab=" . $params['admtab'] ."&opt=3'>". $LANGDATA[''] ."</a></li>\n");

    $opt = S_GET_INT("opt");
    if ( $opt == 1 || $opt == false) {
        $tpl->addto_tplvar("ADM_CONTENT_H2", $LANGDATA['L_GENERAL'] .": ".  $LANGDATA['L_PL_STATE'] );
        $tpl->addto_tplvar("ADM_CONTENT", Admin_GetPluginState("SimpleACL"));
    } else if ( $opt == 2) {
        if(isset($_POST['btnNewRole'])) {
            $msg = SimpleACL_NewRole();
        }
        if (isset($_POST['btnRoleDelete'])) {
            $msg = SimpleACL_DeleteRole();
        }
        SimpleACL_ShowRoles($msg);           
    } else if ( $opt == 3) {
        
    } 
    
    return $tpl->getTPL_file("Admin", "admin_std_content");    
    
}

function SimpleACL_ShowRoles($msg) {
    global $db, $tpl, $LANGDATA;
   
    !empty($msg) ? $table['ACL_MSG'] = $msg : false;

    $tpl->addto_tplvar("ADM_CONTENT_H2", $LANGDATA['L_GENERAL'] .": ".  $LANGDATA['L_ACL_ROLES'] );

    $table['ADM_TABLE_TH'] = "<th>". $LANGDATA['L_ACL_LEVEL'] ."</th>";
    $table['ADM_TABLE_TH'] .= "<th>". $LANGDATA['L_ACL_ROLE_GROUP'] ."</th>";
    $table['ADM_TABLE_TH'] .= "<th>". $LANGDATA ['L_ACL_ROLE_TYPE'] ."</th>";
    $table['ADM_TABLE_TH'] .= "<th>". $LANGDATA ['L_ACL_ROLE_NAME'] ."</th>";
    $table['ADM_TABLE_TH'] .= "<th>". $LANGDATA ['L_ACL_ROLE_DESC'] ."</th>";
    $table['ADM_TABLE_TH'] .= "<th>". $LANGDATA ['L_ACL_ROLE_ACTIONS'] ."</th>";    
    
    $all_roles = $db->select_all("acl_roles", null, "ORDER BY role_group, level");
    $table['ADM_TABLE_ROW'] = "";
    $group = "";
    foreach ($all_roles as $role) {
        if (!empty($group) && $role['role_group'] != $group) {
            $table['ADM_TABLE_ROW'] .= "<tr class='acl_table_sep'><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
            $group = $role['role_group'];
        } else if (empty($group)) {
            $group = $role['role_group'];
        }
        $table['ADM_TABLE_ROW'] .= "<tr>";
        $table['ADM_TABLE_ROW'] .= "<td>" . $role['level'] . "</td>";
        $table['ADM_TABLE_ROW'] .= "<td>" . $role['role_group'] . "</td>";
        $table['ADM_TABLE_ROW'] .= "<td>" . $role['role_type'] . "</td>";
        $table['ADM_TABLE_ROW'] .= "<td>" . $role['role_name'] . "</td>";
        $table['ADM_TABLE_ROW'] .= "<td>" . $role['role_description'] . "</td>";
        $table['ADM_TABLE_ROW'] .= "<td>"; 
        $table['ADM_TABLE_ROW'] .= "<input type='submit' name='btnRoleDelete' value='{$LANGDATA['L_ACL_DELETE']}' />";
        $table['ADM_TABLE_ROW'] .= "<input type='hidden' name='role_id' value='{$role['role_id']}' />";
        $table['ADM_TABLE_ROW'] .= "</td>";
        $table['ADM_TABLE_ROW'] .= "</tr>";
    }
    $tpl->addto_tplvar("ADM_CONTENT", $tpl->getTPL_file("SimpleACL", "acl_admin_roles", $table));

}

function SimpleACL_NewRole() {
    global $LANGDATA, $db;
    $r_level = S_POST_INT("r_level", 2, 1);
    $r_group = S_POST_CHAR_AZ("r_group", 18, 1);
    $r_type = S_POST_CHAR_AZ("r_type", 14, 1);
    $r_name = S_POST_CHAR_AZ("r_name", 32, 1);    
    $r_description = S_POST_TEXT_UTF8("r_description", 255);
    if ( empty($r_level) || empty($r_group) || empty($r_type) || empty($r_name) ) {
        return $msg = $LANGDATA['L_ACL_E_EMPTY_NEWROLE'];
    }
    
    $insert_ary = array (
        "level" => "$r_level",
        "role_group" => "$r_group",
        "role_type" => "$r_type",
        "role_name" => "$r_name",
        "role_description" => $db->escape_strip($r_description)
    );
    
    $db->insert("acl_roles", $insert_ary );
    return $msg = $LANGDATA['L_ACL_ROLE_SUBMIT_SUCCESFUL'];
    
}

function SimpleACL_DeleteRole() {
    global $db;
    $role_id = S_POST_INT("role_id");
    if(!empty($role_id)) {
        $db->delete("acl_roles", array("role_id" => "$role_id", "LIMIT 1"));
    }
}