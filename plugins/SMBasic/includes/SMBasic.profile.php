<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }


function SMBasic_ProfileScript() {
    global $tpl;
    $script = "";
    if (!check_jsScript("jquery.min.js")) {
        global $external_scripts;
        $external_scripts[] = "jquery.min.js";
        $script .= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\"></script>\n";
    }               
    $script .= $tpl->getScript_fileCode("SMBasic", "profile");
    
    return $script;
}

function SMBasic_ViewProfile() {
    global $tpl, $db, $sm;
    
    $uid = S_GET_INT("viewprofile", 11, 1);
    if (empty($uid)) {
        return false; 
    }
    $v_user = $sm->getUserbyID($uid);

    do_action("common_web_structure");
    $tpl->getCSS_filePath("SMBasic");
    $tpl->getCSS_filePath("SMBasic", "SMBasic-mobile");    
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("SMBasic", "viewprofile", $v_user));

}
function SMBasic_ProfileChange() {
    global $LANGDATA, $config, $db; 
    
    if( empty($_POST['cur_password']) ||  strlen ($_POST['cur_password']) <  $config['smbasic_min_password']) {
       $response[] = array("status" => "1", "msg" => $LANGDATA['L_ERROR_PASSWORD_EMPTY_SHORT']);
       echo json_encode($response, JSON_UNESCAPED_SLASHES);
       return false;
    }    
    if (!$password = S_POST_CHAR_AZNUM("cur_password", $config['smbasic_max_password'], $config['smbasic_min_password'] )) { //TODO only accept AZ_NUM no special chars
       $response[] = array("status" => "2", "msg" => $LANGDATA['L_ERROR_PASSWORD']);
       echo json_encode($response, JSON_UNESCAPED_SLASHES);
       return false;        
    }   
    if ( (!empty($_POST['new_password']) && empty($_POST['r_password']) ) ||
            (!empty($_POST['r_password']) && empty($_POST['new_password']) )
    ) {
        $response[] = array("status" => "3", "msg" => $LANGDATA['L_ERROR_NEW_BOTH_PASSWORD']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;                
    }
    if ( (!empty($_POST['new_password']) && !empty($_POST['r_password'])) &&
            ((strlen($_POST['new_password']) < $config['smbasic_min_password']) ||
            (strlen($_POST['r_password']) < $config['smbasic_min_password']))
    ) {
        $response[] = array("status" => "3", "msg" => $LANGDATA['L_ERROR_NEWPASS_TOOSHORT']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;         
    }   
    if ( $_POST['new_password'] != $_POST['r_password']) {
       $response[] = array("status" => "3", "msg" => $LANGDATA['L_ERROR_NEW_PASSWORD_NOTMATCH']);
       echo json_encode($response, JSON_UNESCAPED_SLASHES);
       return false;           
    } 
    if ( ( $config['smbasic_need_username'] == 1) && ( $config['smbasic_can_change_username'] == 1) ){
        if(empty($_POST['username'])) {
           $response[] = array("status" => "4", "msg" => $LANGDATA['L_USERNAME_EMPTY']);
           echo json_encode($response, JSON_UNESCAPED_SLASHES);
           return false;                
        } 
        if (strlen($_POST['username']) < $config['smbasic_min_username'] ) {
           $response[] = array("status" => "4", "msg" => $LANGDATA['L_USERNAME_SHORT']);
           echo json_encode($response, JSON_UNESCAPED_SLASHES);
           return false;                        
        }
        if (strlen($_POST['username']) > $config['smbasic_max_username'] ) {
           $response[] = array("status" => "4", "msg" => $LANGDATA['L_USERNAME_LONG']);
           echo json_encode($response, JSON_UNESCAPED_SLASHES);
           return false;                        
        }            
        if ( ($username = S_POST_CHAR_AZNUM("username", $config['smbasic_max_username'], $config['smbasic_min_username'])) == false) { 
           $response[] = array("status" => "4", "msg" => $LANGDATA['L_USERNAME_CHARS']);
           echo json_encode($response, JSON_UNESCAPED_SLASHES);
           return false;                                    
        }
    }   
    if ( ( $config['smbasic_need_email'] == 1) && ( $config['smbasic_can_change_email'] == 1) ){
        if ( ($email = S_POST_EMAIL("email")) == false ) {
           $response[] = array("status" => "4", "msg" => $LANGDATA['L_ERROR_EMAIL']);
           echo json_encode($response, JSON_UNESCAPED_SLASHES);
           return false;                                    
        } 
        if (strlen($email) > $config['smbasic_max_email'] ) {
           $response[] = array("status" => "4", "msg" => $LANGDATA['L_EMAIL_LONG']);
           echo json_encode($response, JSON_UNESCAPED_SLASHES);
           return false;                        
        }
    }
       
    $password_encrypted = do_action("encrypt_password", $password);

    $query = $db->select_all("users", array("uid" => "{$_SESSION['uid']}", "password" => "$password_encrypted"), "LIMIT 1");
    if($db->num_rows($query) <= 0) {
       $response[] = array("status" => "2", "msg" => $LANGDATA['L_WRONG_PASSWORD']);
       echo json_encode($response, JSON_UNESCAPED_SLASHES);
       return false;         
    } else {
        $user = $db->fetch($query);
    }        
    if ( ( $config['smbasic_need_username'] == 1) &&
            ( $config['smbasic_can_change_username'] == 1) &&
            ( $user['username'] != $_POST['username']) ){        

        $query = $db->select_all("users", array("username" => "$username"), "LIMIT 1");
        if ($db->num_rows($query) > 0) {
           $response[] = array("status" => "4", "msg" => $LANGDATA['L_ERROR_USERNAME_EXISTS']);
           echo json_encode($response, JSON_UNESCAPED_SLASHES);
            return false;             
        }
    }        
    if ( ( $config['smbasic_need_email'] == 1) &&
            ( $config['smbasic_can_change_email'] == 1) &&
            ( $user['email'] != $_POST['email'] )  ){        

        $query = $db->select_all("users", array("email" => "$email"), "LIMIT 1");
        if ($db->num_rows($query) > 0) {
           $response[] = array("status" => "5", "msg" => $LANGDATA['L_ERROR_EMAIL_EXISTS']);
           echo json_encode($response, JSON_UNESCAPED_SLASHES);
            return false;             
        }               
    }     
    if ( ($config['smbasic_need_username'] == 0) ||  
            ($config['smbasic_can_change_username'] == 0) || 
            ($username == $user['username']) ) {
        unset($username);
    }
    if ( ($config['smbasic_need_email'] == 0) ||  
            ($config['smbasic_can_change_email'] == 0) ||
            ($email == $user['email']) ) {
        unset($email);
    }    
    //CHECK if something need change
    if ( (empty($email)) && (empty($username)) && 
            (empty($_POST['new_password'])) && (empty($_POST['r_password'])) ) {
        $response[] = array("status" => "6", "msg" => $LANGDATA['L_NOTHING_CHANGE']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;                     
    }
    
    $q_set_ary = [];
    if (( $config['smbasic_need_username'] == 1) && ( $config['smbasic_can_change_username'] == 1) && ( !empty($username) )) {
            $q_set_ary['username'] = $username;
    }    
    if (( $config['smbasic_need_email'] == 1) && ( $config['smbasic_can_change_email'] == 1) && ( !empty($email) )) {
        $q_set_ary["email"] = $email;
    }       
    if (!empty($_POST['new_password'])) {
        if  ( ($new_password = S_POST_CHAR_AZNUM("new_password", $config['smbasic_max_password'], $config['smbasic_min_password'] )) != false) { //TODO ONLY AZ NUM no special
            $new_password_encrypt = do_action("encrypt_password", $new_password);
            $q_set_ary['password'] = $new_password_encrypt;
        }
    }   

    $db->update("users", $q_set_ary, array("uid" => "{$_SESSION['uid']}"), "LIMIT 1");

    $profile_url = $config['WEB_URL'] . "profile.php";
    $response[] = array("status" => "ok", "msg" => $LANGDATA['L_UPDATE_SUCCESSFUL'] , "url" => "$profile_url");    
    echo json_encode($response, JSON_UNESCAPED_SLASHES);

    return false;
}
