<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SMBasic_AdminInit() {
    register_action("add_admin_menu", "SMBasic_AdminMenu", "5"); 
}

function SMBasic_AdminMenu($params) {   
    $tab_num = 100; //TODO: A WAY TO ASSIGN UNIQ NUMBERS
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_content", "SMBasic_AdminContent");        
        return "<li class='tab_active'><a href='?admtab=$tab_num'>SMBasic</a></li>";
    } else {
        return "<li><a href='?admtab=$tab_num'>SMBasic</a></li>";
    }
}

function SMBasic_AdminContent($params) {
    global $LANGDATA, $tpl;
    
    includePluginFiles("SMBasic", 1);

    $tpl->addto_tplvar("ADM_ASIDE_OPTION", "<li><a href='?admtab=" . $params['admtab'] ."&opt=1'>". $LANGDATA['L_PL_STATE'] ."</a></li>\n" );                               
    $tpl->addto_tplvar("ADM_ASIDE_OPTION", "<li><a href='?admtab=" . $params['admtab'] ."&opt=2'>". $LANGDATA['L_SM_SEARCH_USER'] ."</a></li>\n");
    $tpl->addto_tplvar("ADM_ASIDE_OPTION", "<li><a href='?admtab=" . $params['admtab'] ."&opt=3'>". $LANGDATA['L_SM_USERS_LIST'] ."</a></li>\n");

    $opt = S_GET_INT("opt");
    if ( $opt == 1 || $opt == false) {
        $tpl->addto_tplvar("ADM_CONTENT_H2", $LANGDATA['L_GENERAL'] .": ".  $LANGDATA['L_PL_STATE'] );
        $tpl->addto_tplvar("ADM_CONTENT", Admin_GetPluginState("SMBasic"));       
    } else if ( $opt == 2) {
        SMBasic_UserSearch();
    } else if ( $opt == 3) {
        SMBasic_UserList();
    }
    
    return $tpl->getTPL_file("Admin", "admin_std_content");
}


function SMBasic_UserSearch() {
    global $LANGDATA, $tpl;
    
    $tpl->addto_tplvar("ADM_CONTENT_H2", $LANGDATA['L_SM_SEARCH_USER']);
    $tpl->addto_tplvar("ADM_CONTENT", $LANGDATA['L_SM_USERS_DESC']);        
}

function SMBasic_UserList() {
    global $LANGDATA, $tpl, $sm;
    
    
    $tpl->addto_tplvar("ADM_CONTENT_H2", $LANGDATA['L_SM_USERS_LIST']);
    $tpl->addto_tplvar("ADM_CONTENT", $LANGDATA['L_SM_USERS_LIST_DESC']);            
    
    $users_list = $sm->getAllUsersArray();
    
    
    
    $table['ADM_TABLE_TH']  = "<th>". $LANGDATA ['L_SM_UID'] ."</th>";
    $table['ADM_TABLE_TH'] .= "<th>". $LANGDATA ['L_SM_USERNAME'] ."</th>";
    $table['ADM_TABLE_TH'] .= "<th>". $LANGDATA ['L_EMAIL'] ."</th>";
    $table['ADM_TABLE_TH'] .= "<th>". $LANGDATA ['L_SM_REGISTERED'] ."</th>";
    $table['ADM_TABLE_TH'] .= "<th>". $LANGDATA ['L_SM_LASTLOGIN'] ."</th>";
    
    $active['ADM_TABLE_ROW'] = "";
    $inactive['ADM_TABLE_ROW'] = "";
            
    foreach ($users_list as $user) {
        if ($user['active'] == 0) {        
            $active['ADM_TABLE_ROW'] .= "<tr>";
            $active['ADM_TABLE_ROW'] .= "<td>" . $user['uid'] . "</td>";
            $active['ADM_TABLE_ROW'] .= "<td><a href='?uid={$user['uid']}'>" . $user['username'] . "</a></td>";
            $active['ADM_TABLE_ROW'] .= "<td>" . $user['email'] . "</td>";
            $active['ADM_TABLE_ROW'] .= "<td>" . format_date($user['regdate']) . "</td>";
            $active['ADM_TABLE_ROW'] .= "<td>" . $user['last_login'] . "</td>";
            $active['ADM_TABLE_ROW'] .= "</tr>";
        } else {
            $inactive['ADM_TABLE_ROW'] .= "<tr>";
            $inactive['ADM_TABLE_ROW'] .= "<td>" . $user['uid'] . "</td>";
            $inactive['ADM_TABLE_ROW'] .= "<td><a href='?uid={$user['uid']}'>" . $user['username'] . "</a></td>";
            $inactive['ADM_TABLE_ROW'] .= "<td>" . $user['email'] . "</td>";
            $inactive['ADM_TABLE_ROW'] .= "<td>" . format_date($user['regdate']) . "</td>";
            $inactive['ADM_TABLE_ROW'] .= "<td>" . $user['last_login'] . "</td>";
            $inactive['ADM_TABLE_ROW'] .= "</tr>";            
        }
    }
    
    $active['ADM_TABLE_TITLE'] = "<h3>". $LANGDATA['L_SM_USERS_ACTIVE'] ."</h3>";
    $inactive['ADM_TABLE_TITLE'] = "<h3>". $LANGDATA['L_SM_USERS_INACTIVE'] . "</h3>";
    
    $content = $tpl->getTPL_file("SMBasic", "adm_memberlist", array_merge($table, $active));
    $content .= $tpl->getTPL_file("SMBasic", "adm_memberlist", array_merge($table, $inactive));
    $tpl->addto_tplvar("ADM_CONTENT", $content); 
}