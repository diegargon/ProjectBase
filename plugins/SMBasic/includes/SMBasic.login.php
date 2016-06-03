<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SMBasic_Login() {
    global $config, $LANGDATA;
    
    if ( (($email = S_POST_EMAIL("email")) != false) &&
        ($email != null) &&
        (($password = SMBasic_validate_password($_POST['password']))!= false) &&
        ($password != null)
    ){
        $password = do_action("encrypt_password", $password);
        if(empty($password)) {
            //TODO BETTER ERROR MSG
            echo " {$LANGDATA['L_ERROR_INTERNAL']}: 001";
            exit(0);
        }
        $response = [];       
        $q = "SELECT * FROM " . $config['DB_PREFIX'] . "users WHERE email = '$email' AND password = '$password' LIMIT 1";       
        $query = db_query($q);
        if ($user = db_fetch($query)) {
            if($user['active'] == 0) {
                    SMBasic_setSession($user);
                    if ( ($config['smbasic_session_persistence']) && !empty($_POST['rememberme'])  ){                                 
                        SMBasic_setCookies(S_SESSION_CHAR_AZNUM("sid", 32), S_SESSION_INT("uid", 11));
                    }                    
                    $response[] = array("status" => "ok", "msg" => $config['WEB_URL']);                
            } else {
                $response[] = array("status" => "error", "msg" => $LANGDATA['L_ACCOUNT_INACTIVE']);
                if($user['active'] > 0) { //-1 disable by admin not send email
                    $mail_msg = SMBasic_create_reg_mail($user['active']);
                    mail($user['email'], $LANGDATA['L_REG_EMAIL_SUBJECT'], $mail_msg); 
                }
            }
        } else {            
            $response[] = array("status" => "error", "msg" => $LANGDATA['L_ERROR_EMAILPASSWORD'] );
        }
        db_free_result($query);
    } else {
            $response[] = array("status" => "error", "msg" => $LANGDATA['L_ERROR_EMAILPASSWORD']);
    }
    echo json_encode($response, JSON_UNESCAPED_SLASHES);
}


function SMBasic_user_activate_account() {
    global $config;
    
    if ( ($active = S_GET_INT("active", 12)) == false) {
        return false;
    }
    $q = "SELECT * FROM {$config['DB_PREFIX']}users WHERE active = '$active'";
    $query = db_query($q);
    if(db_num_rows($query) <= 0) {
        return false;
    } else {
        $q = "UPDATE {$config['DB_PREFIX']}users SET active = '0' WHERE active='$active'";
        $query = db_query($q);
    }
    
    return true;
}

function SMBasic_RequestResetOrActivation() {
    global $LANGDATA, $config;

    if ( ($email = S_POST_EMAIL("email")) == false ) {
        $response[] = array("status" => "1", "msg" => $LANGDATA['L_ERROR_EMAIL']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;                                    
    } 
    if (strlen($email) > $config['smbasic_max_email'] ) {
        $response[] = array("status" => "1", "msg" => $LANGDATA['L_EMAIL_LONG']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;                        
    }  
    $q = "SELECT * FROM {$config['DB_PREFIX']}users WHERE email='$email' LIMIT 1";
    $query = db_query($q);
    if (db_num_rows($query) <= 0) {
        $response[] = array("status" => "1", "msg" => $LANGDATA['L_ERROR_EMAIL_NOEXISTS']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;             
    } else {
        $user = db_fetch($query);
        if($user['active'] > 1) {
            $mail_msg = SMBasic_create_reg_mail($user['active']);
            mail($email, $LANGDATA['L_REG_EMAIL_SUBJECT'], $mail_msg);            
            $response[] = array("status" => "2", "msg" => $LANGDATA['L_ACTIVATION_EMAIL']);
            echo json_encode($response, JSON_UNESCAPED_SLASHES);
            return false;             
        } else {            
            $reset = mt_rand(11111111, 2147483647);
            $q = "UPDATE {$config['DB_PREFIX']}users SET reset = '$reset' WHERE email = '$email'";
            db_query($q);
            $URL = "{$config['WEB_URL']}". "/{$config['WEB_LANG']}/". "login.php" . "?reset=$reset&email=$email";
            $msg = $LANGDATA['L_RESET_EMAIL_MSG'] . "\n" ."$URL"; 
            mail($email, $LANGDATA['L_RESET_EMAIL_SUBJECT'], $msg);
            $response[] = array("status" => "2", "msg" => $LANGDATA['L_RESET_EMAIL']);
            echo json_encode($response, JSON_UNESCAPED_SLASHES);
            return false;  
        }
    }    

    return false;                         
}



function SMBasic_user_reset_account() {
    global $config, $LANGDATA;
    
    $reset = S_GET_INT('reset');
    $email = S_GET_EMAIL('email');
    if ($reset == false || $email == false) {
        return false;
    }
    $q = "SELECT * FROM {$config['DB_PREFIX']}users WHERE email = '$email' AND reset = '$reset'";
    $query = db_query($q);
    if (db_num_rows($query) > 0) {
        $user = db_fetch($query);        
        $password = SMBasic_randomPassword();
        $password_encrypted = do_action("encrypt_password", $password);
        $q = "UPDATE {$config['DB_PREFIX']}users SET password = '$password_encrypted', reset = '0' WHERE uid = '{$user['uid']}' ";
        db_query($q);
        $URL = "{$config['WEB_URL']}". "/{$config['WEB_LANG']}/". "login.php"; 
        $msg = $LANGDATA['L_RESET_SEND_NEWMAIL_MSG'] . "\n" . "$password\n" ."$URL"; 
        mail($email, $LANGDATA['L_RESET_SEND_NEWMAIL_SUBJECT'], $msg);
        echo $LANGDATA['L_RESET_PASSWORD_SUCCESS']; 
        exit(0); // TODO MSG RESET OK
    } else {
        return false;
    }
}

function SMBasic_randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = [];
    $alphaLength = strlen($alphabet) - 1; 
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    
    return implode($pass);
}


function SMBasic_LoginScript() {
    $script = "";    
    if (!check_jsScript("jquery.min.js")) {
        global $external_scripts;
        $external_scripts[] = "jquery.min.js";
        $script .= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\"></script>\n";
    }          
    $script .= getScript_fileCode("SMBasic", "login");
    
    return $script;
}

function SMBasic_validate_password($password) { //TODO FILTER VALFILTER CORE
    global $config;
    return s_char($password, $config['smbasic_max_password']);
}
