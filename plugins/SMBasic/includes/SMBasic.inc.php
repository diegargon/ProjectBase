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

function SMBasic_sessionDestroy() {
    // TODO 
    $_SESSION = [];
    session_destroy();
    SMBasic_clearCookies();    
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

function SMBasic_getUserID () { //track used by externa/ news plugin only ATM
    if(!isset($_SESSION['isLogged']) || $_SESSION['isLogged'] != 1 ) {
        return false;
    } else {
        return S_VAR_INTEGER($_SESSION['uid'], 11);
    }
}

function SMBasic_sessionToken() {
    return  md5(uniqid(rand(), true));
}