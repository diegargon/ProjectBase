<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }


function SMBasic_ProfileScript() {
    $script = "";
    if (!check_jsScript("jquery.min.js")) {
        global $external_scripts;
        $external_scripts[] = "jquery.min.js";
        $script .= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\"></script>\n";
    }               
    $script .= getScript_fileCode("SMBasic", "profile");
    
    return $script;
}

function SMBasic_ProfileChange() {
    global $LANGDATA, $config; 
    
    if( empty($_POST['cur_password']) ||  strlen ($_POST['cur_password']) <  $config['smbasic_min_password']) {
       $response[] = array("status" => "1", "msg" => $LANGDATA['L_ERROR_PASSWORD_EMPTY_SHORT']);
       echo json_encode($response, JSON_UNESCAPED_SLASHES);
       return false;
    }    
    if (!$password = s_char($_POST['cur_password'], $config['smbasic_max_password'] )) {  //TODO/FIX FILTER PASSWORD
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
    if ( ( $config['smbasic_need_username'] == 1) &&
            ( $config['smbasic_can_change_username'] == 1)
    ){
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
        //if ( ($username = s_char($_POST['username'], $config['smbasic_max_username'])) == false) { 
        if ( ($username = S_POST_CHAR_AZNUM("username", $config['smbasic_max_username'], $config['smbasic_max_username'])) == false) { 
           $response[] = array("status" => "4", "msg" => $LANGDATA['L_USERNAME_CHAR']);
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
    $q = "SELECT * FROM {$config['DB_PREFIX']}users WHERE uid = '{$_SESSION['uid']}' AND password = '$password_encrypted' LIMIT 1";
    $query = db_query($q);
    if(db_num_rows($query) <= 0) {
       $response[] = array("status" => "2", "msg" => $LANGDATA['L_WRONG_PASSWORD']);
       echo json_encode($response, JSON_UNESCAPED_SLASHES);
       return false;         
    } else {
        $user = db_fetch($query);
    }        
    if ( ( $config['smbasic_need_username'] == 1) &&
            ( $config['smbasic_can_change_username'] == 1) &&
            ( $user['username'] != $_POST['username']) ){        
        $q = "SELECT * FROM {$config['DB_PREFIX']}users WHERE username='$username' LIMIT 1";
        $query = db_query($q);
        if (db_num_rows($query) > 0) {
           $response[] = array("status" => "4", "msg" => $LANGDATA['L_ERROR_USERNAME_EXISTS']);
           echo json_encode($response, JSON_UNESCAPED_SLASHES);
            return false;             
        }
    }        
    if ( ( $config['smbasic_need_email'] == 1) &&
            ( $config['smbasic_can_change_email'] == 1) &&
            ( $user['email'] != $_POST['email'] )  ){        
        $q = "SELECT * FROM {$config['DB_PREFIX']}users WHERE email='$email' LIMIT 1";
        $query = db_query($q);
        if (db_num_rows($query) > 0) {
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
    
    $need_coma = 0; // huh!
    
    $q = "UPDATE {$config['DB_PREFIX']}users SET ";
   
    if (( $config['smbasic_need_username'] == 1) && ( $config['smbasic_can_change_username'] == 1) && ( !empty($username) )) {
        $q .= "username = '$username'";
        $need_coma = 1;
    }    
    if (( $config['smbasic_need_email'] == 1) && ( $config['smbasic_can_change_email'] == 1) && ( !empty($email) )) {
        if ($need_coma) {
            $q .= ", email = '$email'";
        } else {
            $q .= "email = '$email'";
            $need_coma = 1;
        }
    }       
    if (!empty($_POST['new_password'])) {
        if  ( ($new_password = s_char($_POST['new_password'], $config['smbasic_max_password'])) != false) { //FIX password validation
            $new_password_encrypt = do_action("encrypt_password", $new_password);
            if ($need_coma) {               
               $q .= ", password = '$new_password_encrypt'";
           } else {
               $q .= " password = '$new_password_encrypt'";
               $need_coma = 1;
           }
        }
    }   

    $q .= " WHERE uid = {$_SESSION['uid']} LIMIT 1";    
    db_query($q);
    $profile_url = $config['WEB_URL'] . "profile.php";
    $response[] = array("status" => "ok", "msg" => $LANGDATA['L_UPDATE_SUCCESSFUL'] , "url" => "$profile_url");    
    echo json_encode($response, JSON_UNESCAPED_SLASHES);

    return false;
}
