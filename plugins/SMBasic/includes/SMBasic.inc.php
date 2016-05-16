<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */

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

function SMBasic_validate_email($email) {
    global $config;
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);    
    $email = s_char($_POST['email1'], $config['smbasic_max_email']);
    return $email;
}

function SMBasic_validate_user_agent($user_agent) {
    return s_char($user_agent, 0);
}

function SMBasic_validate_ip($ip) {
    return filter_var($ip, FILTER_VALIDATE_IP);
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
    $ip = SMBasic_validate_ip($_SERVER['REMOTE_ADDR']);
    $user_agent = SMBasic_validate_user_agent($_SERVER['HTTP_USER_AGENT']);
    $q = "DELETE FROM {$config['DB_PREFIX']}sessions WHERE session_uid = {$user['uid']}";
    db_query($q);
    $q = "INSERT INTO $config[DB_PREFIX]sessions ("
     . "session_id, session_uid, session_ip, session_browser, session_expire"
     . ")VALUES("
     . "'{$_SESSION['sid']}', '{$user['uid']}', '$ip', '$user_agent', '$session_expire'"
     . ");";
     
     db_query($q);

     if (
             ($config['smbasic_session_persistence']) && 
             ($rememberme = s_bool($_POST['rememberme1']))
             ){
  
         SMBasic_setCookies($_SESSION['sid'], $_SESSION['uid']);
     }
        
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
        . " WHERE session_id = '{$_SESSION['sid']}' AND session_uid = '{$_SESSION['uid']}'";
    $query = db_query($q);
    
    if (!$query) {
        SMBasic_sessionDestroy();
        db_free_result($query);
        return false;
    } else {
        $row = db_fetch($query);
        if ($row['session_expire'] < $now) {
            SMBasic_sessionDestroy(); 
            db_free_result($query);
            return false;
        } else {
            $q = "UPDATE {$config['DB_PREFIX']}sessions"
            . " SET session_expire = '$next_expire'"
            . " WHERE session_uid = '{$_SESSION['uid']}'";
            db_query($q);
        }
    }
    db_free_result($query);
    return true;
}


function SMBasic_checkCookies() {
    global $config;
    $cookie_uid = $config['smbasic_cookie_prefixname']."uid";
    $cookie_sid = $config['smbasic_cookie_prefixname']."sid";
    if (isset($_COOKIE[$cookie_uid]) && isset($_COOKIE[$cookie_sid])) {
        $_SESSION['uid'] = s_num($_COOKIE[$cookie_uid], 11);
        $_SESSION['sid'] = s_char($_COOKIE[$cookie_sid], 32);
        if(SMBasic_checkSession()) {
            SMBasic_getUserbyID($_SESSION['uid']);
            return true;
        } else {
            SMBasic_unset_session();
            return false;
        }
    }
    return false;
}


function SMBasic_getUserbyID($uid) {
    global $config;
   
    $q = "SELECT * FROM $config[DB_PREFIX]users WHERE uid = '$uid'";
    $query = db_query($q);
    if ($user = db_fetch($query)) {

        $_SESSION['username'] = $user['username'];
    }
    
}

function SMBasic_unset_session() {
    unset($_SESSION);
}


function SMBasic_Login() {
    global $config;
    
    if ( 
        (($email = SMBasic_validate_email($_POST['email1'])) != false) &&
        ($email != null) &&
        (($password = SMBasic_validate_password($_POST['password1']))!= false) &&
        ($password != null)
        )
    {
        if(action_isset("encrypt_password") == false) {           
           $password = SMBasic_encrypt_password($password);
        } else {
            $password = do_action("encrypt_password");
        }
        if(!isset($password)) {
            print "Internal Error password mechanism";
            exit(0);
        }
       $response = [];
       
        $q = "SELECT * FROM " . $config['DB_PREFIX'] . "users WHERE email = '$email' AND password = '$password'";
       
        $query = db_query($q);
        if ($user = db_fetch($query)) {
            SMBasic_setSession($user);
            $response[] = array("status" => "ok", "msg" => $config['WEB_URL']);
        } else {
            $response[] = array("status" => "error", "msg" => "Email o contraseña incorrectos");
        }
        db_free_result($query);
    } else {
            $response[] = array("status" => "error", "msg" => "Email o contraseña incorrectos");
    }
    echo json_encode($response, JSON_UNESCAPED_SLASHES);
}