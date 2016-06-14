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
    global $config, $LANGDATA, $tpl, $sm;
    
    $tpl->addto_tplvar("ADM_CONTENT_H2", $LANGDATA['L_SM_SEARCH_USER']);
    $tpl->addto_tplvar("ADM_CONTENT", $LANGDATA['L_SM_USERS_DESC']);
    
    $content = "<form action='' method='post'>";
    $content .= "<label for='glob'>{$LANGDATA['L_SM_GLOB']}: </label><input type='checkbox' name='posted_glob' id='glob' value='1' />";
    $content .= "<label for='email'>{$LANGDATA['L_EMAIL']}: </label><input type='checkbox' name='posted_email' id='email' value='1' />";
    $content .= "<input type='text' maxlength='32' minlength='3' name='search_user' id='search_user' required />";
    $content .= "<input type='submit' name='btnSearchUser' id='btnSearchUser' />";
    $content .= "</form><br/>";
    isset($_POST['posted_glob']) ? $glob = 1 : $glob = 0;
    isset($_POST['posted_email']) ? $email = 1 : $email = 0;
    $s_string = S_POST_STRICT_CHARS("search_user", 32, 1);


    if (!empty($_POST['btnSearchUser']) && !empty($s_string)) {
        if ($users_ary = $sm->searchUser($s_string, $email, $glob)) {
            $table['ADM_TABLE_TH']  = "<th>". $LANGDATA ['L_SM_UID'] ."</th>";
            $table['ADM_TABLE_TH'] .= "<th>". $LANGDATA ['L_SM_USERNAME'] ."</th>";
            $table['ADM_TABLE_TH'] .= "<th>". $LANGDATA ['L_EMAIL'] ."</th>";
            $table['ADM_TABLE_TH'] .= "<th>". $LANGDATA ['L_SM_REGISTERED'] ."</th>";
            $table['ADM_TABLE_TH'] .= "<th>". $LANGDATA ['L_SM_LASTLOGIN'] ."</th>";            
            $table['ADM_TABLE_ROW'] = "";
            foreach($users_ary as $user_match) {
                $table['ADM_TABLE_ROW'] .= "<tr>";
                $table['ADM_TABLE_ROW'] .= "<td>" . $user_match['uid'] . "</td>";
                $table['ADM_TABLE_ROW'] .= "<td><a href='/profile.php?lang={$config['WEB_LANG']}&viewprofile={$user_match['uid']}'>" . $user_match['username'] . "</a></td>";
                $table['ADM_TABLE_ROW'] .= "<td>" . $user_match['email'] . "</td>";
                $table['ADM_TABLE_ROW'] .= "<td>" . format_date($user_match['regdate']) . "</td>";
                $table['ADM_TABLE_ROW'] .= "<td>" . $user_match['last_login'] . "</td>";
                $table['ADM_TABLE_ROW'] .= "</tr>";                
            }
            $content .= $tpl->getTPL_file("SMBasic", "memberlist", $table);
        }
    }
    $tpl->addto_tplvar("ADM_CONTENT", $content); 
}

function SMBasic_UserList() {
    global $config, $LANGDATA, $tpl, $sm;
    
    
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
            $active['ADM_TABLE_ROW'] .= "<td><a href='/profile.php?lang={$config['WEB_LANG']}&viewprofile={$user['uid']}'>" . $user['username'] . "</a></td>";
            $active['ADM_TABLE_ROW'] .= "<td>" . $user['email'] . "</td>";
            $active['ADM_TABLE_ROW'] .= "<td>" . format_date($user['regdate']) . "</td>";
            $active['ADM_TABLE_ROW'] .= "<td>" . format_date($user['last_login']) . "</td>";
            $active['ADM_TABLE_ROW'] .= "</tr>";
        } else {
            $inactive['ADM_TABLE_ROW'] .= "<tr>";
            $inactive['ADM_TABLE_ROW'] .= "<td>" . $user['uid'] . "</td>";
            $inactive['ADM_TABLE_ROW'] .= "<td><a href='/profile.php?lang={$config['WEB_LANG']}&viewprofile={$user['uid']}'>" . $user['username'] . "</a></td>";
            $inactive['ADM_TABLE_ROW'] .= "<td>" . $user['email'] . "</td>";
            $inactive['ADM_TABLE_ROW'] .= "<td>" . format_date($user['regdate']) . "</td>";
            $inactive['ADM_TABLE_ROW'] .= "<td>" . format_date($user['last_login']) . "</td>";
            $inactive['ADM_TABLE_ROW'] .= "</tr>";            
        }
    }
    
    $active['ADM_TABLE_TITLE'] = "<h3>". $LANGDATA['L_SM_USERS_ACTIVE'] ."</h3>";
    $inactive['ADM_TABLE_TITLE'] = "<h3>". $LANGDATA['L_SM_USERS_INACTIVE'] . "</h3>";
    
    $content = $tpl->getTPL_file("SMBasic", "memberlist", array_merge($table, $active));
    $content .= $tpl->getTPL_file("SMBasic", "memberlist", array_merge($table, $inactive));
    $tpl->addto_tplvar("ADM_CONTENT", $content); 
}