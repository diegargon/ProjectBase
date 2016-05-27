<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SMBasic_encrypt_password($password) {
    global $config;
    
    if($config['smbasic_use_salt']) {
        return hash('sha512', md5($password . $config['smbasic_salt'] ));
    } else {
        return hash('sha512', $password);      
    }
}

function SMBasic_sessionToken() {
    return  md5(uniqid(rand(), true));
}

function SMBasic_validate_password($password) {
    global $config;
    return s_char($password, $config['smbasic_max_password']);
}

function SMBasic_sessionDestroy() {
    // TODO 
    $_SESSION = [];
    session_destroy();
    SMBasic_clearCookies();    
}

function SMBasic_setSession($user) {
    global $config;

    $session_expire = time() + $config['smbasic_session_expire'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['uid']  = $user['uid'];
    $_SESSION['sid'] = SMBasic_sessionToken();
    $_SESSION['isLogged'] = 1;
    $ip = S_SERVER_REMOTE_ADDR();    
    $user_agent = S_SERVER_USER_AGENT();
    
    $q = "DELETE FROM {$config['DB_PREFIX']}sessions WHERE session_uid = {$user['uid']}";
    db_query($q);
    $q = "INSERT INTO $config[DB_PREFIX]sessions ("
     . "session_id, session_uid, session_ip, session_browser, session_expire"
     . ")VALUES("
     . "'{$_SESSION['sid']}', '{$user['uid']}', '$ip', '$user_agent', '$session_expire'"
     . ");";     
     db_query($q);
}

function SMBasic_setCookies($sid, $uid) {
    global $config;
    $cookie_expire = time() + $config['smbasic_cookie_expire'];
    
    $cookie_name_sid = $config['smbasic_cookie_prefixname']  . "sid";
    $cookie_name_uid = $config['smbasic_cookie_prefixname'] . "uid";            
    setcookie($cookie_name_sid,$sid , $cookie_expire,'/');
    setcookie($cookie_name_uid,$uid , $cookie_expire,'/');
}

function SMBasic_clearCookies() {
    global $config;
    $cookie_name_sid = $config['smbasic_cookie_prefixname'] . "sid";
    $cookie_name_uid = $config['smbasic_cookie_prefixname'] . "uid"; 
    unset($_COOKIE[$cookie_name_sid]);
    unset($_COOKIE[$cookie_name_uid]);
    setcookie($cookie_name_sid, 0, time()-3600, '/');
    setcookie($cookie_name_uid, 0, time()-3600, '/');
}

function SMBasic_checkSession() {
    global $config;
    $now = time();
    $next_expire = time() + $config['smbasic_session_expire'];
    
    $q = "SELECT * FROM {$config['DB_PREFIX']}sessions"
        . " WHERE session_id = '{$_SESSION['sid']}' AND session_uid = '{$_SESSION['uid']}' LIMIT 1";
    $query = db_query($q);
    
    if (db_num_rows($query) <= 0) {        
        db_free_result($query);
        return false;
    } else {
        $session = db_fetch($query);
        db_free_result($query);
        if ($config['smbasic_check_ip'] == 1) {
            if(!SMBasic_check_IP($session['session_ip'])) {
                if (SM_DEBUG) { print_debug("SMBasic:IP validated FALSE"); }
                return false;
            }
            if (SM_DEBUG) { print_debug("SMBasic:IP validated OK"); }
        }
        if ($config['smbasic_check_user_agent'] == 1) {
            if(!SMBasic_check_user_agent($session['session_browser'])) {
                if (SM_DEBUG) { print_debug("SMBasic:UserAgent validated FALSE"); }            
                return false;
            }
            if (SM_DEBUG) { print_debug("SMBasic:UserAgent validated OK"); }
        }
        if ($session['session_expire'] < $now) { 
            if (SM_DEBUG) { print_debug("SMBasic: db session expired at $now"); }
            return false;
        } else {
            $q = "UPDATE {$config['DB_PREFIX']}sessions"
            . " SET session_expire = '$next_expire'"
            . " WHERE session_uid = '{$session['session_uid']}'";
            db_query($q);
        }
    }
    
    return true;
}

function SMBasic_checkCookies() {
    global $config;
    $cookie_uid = $config['smbasic_cookie_prefixname']."uid";
    $cookie_sid = $config['smbasic_cookie_prefixname']."sid";
    if (!empty($_COOKIE[$cookie_uid]) && !empty($_COOKIE[$cookie_sid])) {
        $cookie_uid =  s_num($_COOKIE[$cookie_uid], 11);
        $cookie_sid = s_char($_COOKIE[$cookie_sid], 32);
        $q = "SELECT * FROM {$config['DB_PREFIX']}sessions"
            . " WHERE session_id = '$cookie_sid' AND session_uid = '$cookie_uid' LIMIT 1";
        $query = db_query($q);
        if (db_num_rows($query) > 0) {           
            if( ($user = SMBasic_getUserbyID($cookie_uid)) != false ) {
                SMBasic_setSession($user);
                SMBasic_setCookies($_SESSION['sid'], $_SESSION['uid']); 
                return true;
            }
        } else { 
            SMBasic_sessionDestroy();
            db_free_result($query);
        }
    }
    
    return false;
}

function SMBasic_getUserbyID($uid) {
    global $config;
   
    $q = "SELECT * FROM $config[DB_PREFIX]users WHERE uid = '$uid'";
    $query = db_query($q);
    if (db_num_rows($query) <= 0) {
        return false;        
    }
    $user = db_fetch($query);
    return $user;
}

function SMBasic_Login() {
    global $config, $LANGDATA;
    
    if ( 
        (($email = S_POST_EMAIL("email1")) != false) &&
        ($email != null) &&
        (($password = SMBasic_validate_password($_POST['password1']))!= false) &&
        ($password != null)
        )
    {
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
                    if ( ($config['smbasic_session_persistence']) && ($_POST['rememberme1'] == "true") //true without "" not work (javascript true) 
                    ){         
                        SMBasic_setCookies($_SESSION['sid'], $_SESSION['uid']);
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

function SMBasic_Register() {
    global $config, $LANGDATA;
    
    if( 
        ($config['smbasic_need_email'] == 1)  && 
        (($email = S_POST_EMAIL("email1")) == false)) {
        $response[] = array("status" => "1", "msg" => $LANGDATA['L_ERROR_EMAIL']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false; 
    }    
    if(
        ($config['smbasic_need_username'] == 1) && 
        (($username = s_char($_POST['username1'], $config['smbasic_max_username'])) == false)) {
        $response[] = array("status" => "2", "msg" => $LANGDATA['L_ERROR_USERNAME']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }
    if(
        ($config['smbasic_need_username'] == 1) && 
        (strlen($username) < $config['smbasic_min_username']) 
            ) {
        $response[] = array("status" => "2", "msg" => $LANGDATA['L_USERNAME_SHORT'] );    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }    
    if(
        ($password = s_char($_POST['password1'], $config['smbasic_max_password'])) == false ) {
        $response[] = array("status" => "3", "msg" => $LANGDATA['L_ERROR_PASSWORD']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }    
    if (strlen($_POST['password1']) < 8) {
        $response[] = array("status" => "4", "msg" => $LANGDATA['L_ERROR_PASSWORD_MIN']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }    
    $q = "SELECT * FROM {$config['DB_PREFIX']}users WHERE username = '$username'";  //FIX SELECT username or/and mixed with email
    $query = db_query($q);     
    if ((db_num_rows($query)) > 0) {
        $response[] = array("status" => "5", "msg" => $LANGDATA['L_ERROR_USERNAME_EXISTS']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        db_free_result($query);
        return false;                
    }
    $q = "SELECT * FROM {$config['DB_PREFIX']}users WHERE email = '$email'";  //FIX SELECT email or/and mixed with email
    $query = db_query($q);    
    if ((db_num_rows($query)) > 0) {
        $response[] = array("status" => "6", "msg" => $LANGDATA['L_ERROR_EMAIL_EXISTS']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        db_free_result($query);
        return false;                        
    }        
    db_free_result($query);
    $password = do_action("encrypt_password", $password);
    if ($config['smbasic_email_confirmation']) {
        $active = mt_rand(11111111, 2147483647); //Largest mysql init
        $register_message = $LANGDATA['L_REGISTER_OKMSG_CONFIRMATION'];
    } else {
        $active = 1;
        $register_message = $LANGDATA['L_REGISTER_OKMSG'];        
    }    
    $mail_msg = SMBasic_create_reg_mail($active);    
    $q = "INSERT INTO {$config['DB_PREFIX']}users ("
        . "username, password, email, active"
        . ") VALUES ("
        . "'$username', '$password', '$email', '$active');";   
    $query = db_query($q);    
    if($query) {       
       mail($email, $LANGDATA['L_REG_EMAIL_SUBJECT'], $mail_msg);       
       $response[] = array("status" => "ok", "msg" => $register_message, "url" => $config['WEB_URL']);       
       echo json_encode($response, JSON_UNESCAPED_SLASHES); 
    } else {
       $response[] = array("status" => "7", "msg" => $LANGDATA['L_REG_ERROR_WHILE']);
       echo json_encode($response, JSON_UNESCAPED_SLASHES);
    }
    
    return true;      
}

function SMBasic_create_reg_mail($active) {
    global $LANGDATA, $config;
    
    if ($active > 1) {        
        $URL = "http://$_SERVER[HTTP_HOST]". "/{$config['WEB_LANG']}/". "login.php" . "?active=$active";
        $msg = $LANGDATA['L_REG_EMAIL_MSG_ACTIVE'] . "\n" ."$URL";         
    } else {
        $register_message = $LANGDATA['L_REGISTER_OKMSG'];
        $URL = "http://$_SERVER[HTTP_HOST]". "/{$config['WEB_LANG']}/" . "login.php";
        $msg = $LANGDATA['L_REG_EMAIL_MSG_WELCOME'] . "\n" . "$URL";
    }      
    return $msg;
}

function SMBasic_user_activate_account() {
    global $config;
    
    if ( ($active = s_num($_GET['active'], 12)) == false) {
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

function SMBasic_sessionDebugDetails() {
    global $config;
    
    print_debug("<hr><br/><h2>Session Details</h2>");
    print_debug("Time Now: ". format_date(time(),true) ."");
    print_debug("Session VAR ID: {$_SESSION['uid']}");
    print_debug("Session VAR Username: {$_SESSION['username']}");
    print_debug("Session VAR SID:  {$_SESSION['sid']}");
    $q = "SELECT * FROM {$config['DB_PREFIX']}sessions WHERE session_uid = '{$_SESSION['uid']}' AND  session_id = '{$_SESSION['sid']}' LIMIT 1";
    $query = db_query($q);
    $session = db_fetch($query);    
    print_debug("Session DB IP: {$session['session_ip']}");
    print_debug("Session DB Browser: {$session['session_browser']}");
    print_debug("Session DB Create: {$session['session_created']}");
    print_debug("Session DB Expire:" . format_date("{$session['session_expire']}", true) ."");
    print_debug("Session DB Admin: {$session['session_admin']} ");
    print_debug("PHP Session expire: " . ini_get('session.gc_maxlifetime'));
    print_debug("Cookies State:");
    if ( isset($_COOKIE) ) {
        print_debug(" is set");
        print_debug("Cookie Array:");
        foreach ($_COOKIE as $key=>$val)
        {
            print_debug("Cookie $key -> $val");
        }   
        print_debug("<hr>");        
    } else {
        print_debug(" not set");        
    }
}

function SMBasic_check_IP($db_session_ip) {
    $ip = S_SERVER_REMOTE_ADDR();
    if($ip == $db_session_ip) {
        return true;
    }
    
    return false;    
}

function SMBasic_check_user_agent($db_user_agent) {
    $user_agent = S_SERVER_USER_AGENT();
    if ($user_agent == $db_user_agent) {        
        return true;
    }
    
    return false;
}

function SMBasic_ProfileChange() {
    global $LANGDATA, $config; 
    
    if( empty($_POST['cur_password1']) ||  strlen ($_POST['cur_password1']) <  $config['smbasic_min_password']) {
       $response[] = array("status" => "1", "msg" => $LANGDATA['L_ERROR_PASSWORD_EMPTY_SHORT']);
       echo json_encode($response, JSON_UNESCAPED_SLASHES);
       return false;
    }    
    if (!$password = s_char($_POST['cur_password1'], $config['smbasic_max_password'] )) {
       $response[] = array("status" => "2", "msg" => $LANGDATA['L_ERROR_PASSWORD']);
       echo json_encode($response, JSON_UNESCAPED_SLASHES);
       return false;        
    }   
    if ( 
            (!empty($_POST['new_password1']) && empty($_POST['r_password1']) ) ||
            (!empty($_POST['r_password1']) && empty($_POST['new_password1']) )
            ) {
       $response[] = array("status" => "3", "msg" => $LANGDATA['L_ERROR_NEW_BOTH_PASSWORD']);
       echo json_encode($response, JSON_UNESCAPED_SLASHES);
       return false;        
        
    }
    if ( 
            (!empty($_POST['new_password1']) && !empty($_POST['r_password1'])) &&
            ((strlen($_POST['new_password1']) < $config['smbasic_min_password']) ||
            (strlen($_POST['r_password1']) < $config['smbasic_min_password']))
            ) {
       $response[] = array("status" => "3", "msg" => $LANGDATA['L_ERROR_NEWPASS_TOOSHORT']);
       echo json_encode($response, JSON_UNESCAPED_SLASHES);
       return false;        
        
    }   
    if ( $_POST['new_password1'] != $_POST['r_password1']) {
       $response[] = array("status" => "3", "msg" => $LANGDATA['L_ERROR_NEW_PASSWORD_NOTMATCH']);
       echo json_encode($response, JSON_UNESCAPED_SLASHES);
       return false;        
        
    } 
    if (
            ( $config['smbasic_need_username'] == 1) &&
            ( $config['smbasic_can_change_username'] == 1)
        ){
        if(empty($_POST['username1'])) {
           $response[] = array("status" => "4", "msg" => $LANGDATA['L_USERNAME_EMPTY']);
           echo json_encode($response, JSON_UNESCAPED_SLASHES);
           return false;            
            
        } 
        if (strlen($_POST['username1']) < $config['smbasic_min_username'] ) {
           $response[] = array("status" => "4", "msg" => $LANGDATA['L_USERNAME_SHORT']);
           echo json_encode($response, JSON_UNESCAPED_SLASHES);
           return false;                        
        }
        if (strlen($_POST['username1']) > $config['smbasic_max_username'] ) {
           $response[] = array("status" => "4", "msg" => $LANGDATA['L_USERNAME_LONG']);
           echo json_encode($response, JSON_UNESCAPED_SLASHES);
           return false;                        
        }    
        if ( ($username = s_char($_POST['username1'], $config['smbasic_max_username'])) == false) { //FIX function check allowed chars 
           $response[] = array("status" => "4", "msg" => $LANGDATA['L_USERNAME_CHAR']);
           echo json_encode($response, JSON_UNESCAPED_SLASHES);
           return false;                                    
        }
    }   
    if (
            ( $config['smbasic_need_email'] == 1) &&
            ( $config['smbasic_can_change_email'] == 1)
        ){
        if ( 
                ($email = S_POST_EMAIL("email1")) == false
                ) {
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
    if (
            ( $config['smbasic_need_username'] == 1) &&
            ( $config['smbasic_can_change_username'] == 1) &&
            ( $user['username'] != $_POST['username1'])
        ){
        
        $q = "SELECT * FROM {$config['DB_PREFIX']}users WHERE username='$username' LIMIT 1";
        $query = db_query($q);
        if (db_num_rows($query) > 0) {
           $response[] = array("status" => "4", "msg" => $LANGDATA['L_ERROR_USERNAME_EXISTS']);
           echo json_encode($response, JSON_UNESCAPED_SLASHES);
            return false;             
        }
    }        
    if (
            ( $config['smbasic_need_email'] == 1) &&
            ( $config['smbasic_can_change_email'] == 1) &&
            ( $user['email'] != $_POST['email1'] ) 
        ){        
        $q = "SELECT * FROM {$config['DB_PREFIX']}users WHERE email='$email' LIMIT 1";
        $query = db_query($q);
        if (db_num_rows($query) > 0) {
           $response[] = array("status" => "5", "msg" => $LANGDATA['L_ERROR_EMAIL_EXISTS']);
           echo json_encode($response, JSON_UNESCAPED_SLASHES);
            return false;             
        }               
    }     
    if ( 
            ($config['smbasic_need_username'] == 0) ||  
            ($config['smbasic_can_change_username'] == 0) ||
            ($username == $user['username']) 
        ) {
        unset($username);
    }
    if ( 
            ($config['smbasic_need_email'] == 0) ||  
            ($config['smbasic_can_change_email'] == 0) ||
            ($email == $user['email']) 
        ) {
        unset($email);
    }    
    //CHECK if something need change
    if (    
            (empty($email)) &&
            (empty($username)) &&
            (empty($_POST['new_password1'])) &&
            (empty($_POST['r_password1']))
            ) {
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
    if (!empty($_POST['new_password1'])) {
        if  ( ($new_password = s_char($_POST['new_password1'], $config['smbasic_max_password'])) != false) { //FIX password validation
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

function SMBasic_RequestResetOrActivation() {
    global $LANGDATA, $config;

    if ( 
        ($email = S_POST_EMAIL("email1")) == false
    ) {
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
            $URL = "http://$_SERVER[HTTP_HOST]". "/{$config['WEB_LANG']}/". "login.php" . "?reset=$reset&email=$email";
            $msg = $LANGDATA['L_RESET_EMAIL_MSG'] . "\n" ."$URL"; 
            mail($email, $LANGDATA['L_RESET_EMAIL_SUBJECT'], $msg);
            $response[] = array("status" => "2", "msg" => $LANGDATA['L_RESET_EMAIL']);
            echo json_encode($response, JSON_UNESCAPED_SLASHES);
            return false;  
        }
    }    
    $response[] = array("status" => "1", "msg" => $email);
    echo json_encode($response, JSON_UNESCAPED_SLASHES);

    return false;                         
}



function SMBasic_user_reset_account() {
    global $config, $LANGDATA;
    
    $reset = S_GET_INT('reset');
    $email = S_GET_EMAIL('email');
    $q = "SELECT * FROM {$config['DB_PREFIX']}users WHERE email = '$email' AND reset = '$reset'";
    $query = db_query($q);
    if (db_num_rows($query) > 0) {
        $user = db_fetch($query);        
        $password = SMBasic_randomPassword();
        $password_encrypted = do_action("encrypt_password", $password);
        $q = "UPDATE {$config['DB_PREFIX']}users SET password = '$password_encrypted', reset = '0' WHERE uid = '{$user['uid']}' ";
        db_query($q);
        $URL = "http://$_SERVER[HTTP_HOST]". "/{$config['WEB_LANG']}/". "login.php";
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