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
    global $config, $db;
    $now = time();
    $next_expire = time() + $config['smbasic_session_expire'];
    
    //$q = "SELECT * FROM {$config['DB_PREFIX']}sessions"
//        . " WHERE session_id = '{$_SESSION['sid']}' AND session_uid = '{$_SESSION['uid']}' LIMIT 1";
  //  $query = db_query($q);
    
    $query = $db->select_all("sessions", array("session_id" => "{$_SESSION['sid']}", "session_uid" => "{$_SESSION['uid']}"), "LIMIT 1"); //TODO filter SESSION
    
    if ($db->num_rows($query) <= 0) {        
        $db->free($query);
        return false;
    } else {
        $session = $db->fetch($query);
        $db->free($query);
        if ($config['smbasic_check_ip'] == 1) {
            if(!SMBasic_check_IP($session['session_ip'])) {
                print_debug("SMBasic:IP validated FALSE", "SM_DEBUG"); 
                return false;
            }
            print_debug("SMBasic:IP validated OK", "SM_DEBUG");
        }
        if ($config['smbasic_check_user_agent'] == 1) {
            if(!SMBasic_check_user_agent($session['session_browser'])) {
                print_debug("SMBasic:UserAgent validated FALSE", "SM_DEBUG");            
                return false;
            }
            print_debug("SMBasic:UserAgent validated OK", "SM_DEBUG"); 
        }
        if ($session['session_expire'] < $now) { 
            print_debug("SMBasic: db session expired at $now", "SM_DEBUG"); 
            return false;
        } else {
/*            $q = "UPDATE {$config['DB_PREFIX']}sessions"
            . " SET session_expire = '$next_expire'"
            . " WHERE session_uid = '{$session['session_uid']}'";
            db_query($q);
 */
            $db->update("sessions", array("session_expire" => "$next_expire"), array("session_uid" => "{$session['session_uid']}"));
        }
    }
    
    return true;
}

function SMBasic_checkCookies() {
    global $config, $db;

    $cookie_uid = S_COOKIE_INT("{$config['smbasic_cookie_prefixname']}uid", 11);    
    $cookie_sid = S_COOKIE_CHAR_AZNUM("{$config['smbasic_cookie_prefixname']}sid", 32);        
    
    if ($cookie_uid != false && $cookie_sid != false) {
/*        $q = "SELECT * FROM {$config['DB_PREFIX']}sessions"
            . " WHERE session_id = '$cookie_sid' AND session_uid = '$cookie_uid' LIMIT 1";
        $query = db_query($q);
 * 
 */
        $query = $db->select_all("sessions", array("session_id" => "$cookie_sid", "session_uid" => "$cookie_uid"), "LIMIT 1" );
        if ($db->num_rows($query) > 0) {           
            if( ($user = SMBasic_getUserbyID($cookie_uid)) != false ) {                
                SMBasic_setSession($user);
                SMBasic_setCookies(S_SESSION_CHAR_AZNUM("sid", 32), S_SESSION_INT("uid", 11)); //New sid by setSession -> new cookies
                return true;
            }
        } else { 
            SMBasic_sessionDestroy();
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
    global $db;
    
    print_debug("<hr><br/><h2>Session Details</h2>");
    print_debug("Time Now: ". format_date(time(),true) ."");
    print_debug("Session VAR ID: {$_SESSION['uid']}");
    print_debug("Session VAR Username: {$_SESSION['username']}");
    print_debug("Session VAR SID:  {$_SESSION['sid']}");
    
    $query = $db->select_all("sessions", array("session_uid" => "{$_SESSION['uid']}", "session_id" => "{$_SESSION['sid']}"), "LIMIT 1"); //TODO filter $_SESSION
//    $q = "SELECT * FROM {$config['DB_PREFIX']}sessions WHERE session_uid = '{$_SESSION['uid']}' AND  session_id = '{$_SESSION['sid']}' LIMIT 1";
//    $query = db_query($q);
    $session = $db->fetch($query);    
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
    global $config, $db;

    $session_expire = time() + $config['smbasic_session_expire'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['uid']  = $user['uid'];
    $_SESSION['sid'] = SMBasic_sessionToken();
    $_SESSION['isLogged'] = 1;
    $ip = S_SERVER_REMOTE_ADDR();    //FIX: not scape i think check
    $user_agent = S_SERVER_USER_AGENT(); //FIX: not scape i think check
    
    //$q = "DELETE FROM {$config['DB_PREFIX']}sessions WHERE session_uid = {$user['uid']}";    
    //db_query($q);
    $db->delete("sessions", array("session_uid" => "{$user['uid']}"));

/*    
    $q = "INSERT INTO {$config['DB_PREFIX']}sessions ("
     . "session_id, session_uid, session_ip, session_browser, session_expire"
     . ")VALUES("
     . "'{$_SESSION['sid']}', '{$user['uid']}', '$ip', '$user_agent', '$session_expire'"
     . ");";     
     db_query($q);
 * 
 */
    $q_ary = array (
        "session_id" => "{$_SESSION['sid']}",
        "session_uid" => "{$user['uid']}",
        "session_ip" => "$ip",
        "session_browser" => "$user_agent",
        "session_expire" => "$session_expire"                
    );
    $db->insert("sessions", $q_ary);
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
    global $db;
   
/*    $q = "SELECT * FROM $config[DB_PREFIX]users WHERE uid = '$uid'";
    $query = db_query($q);
 * 
 */
    $query = $db->select_all("users", array("uid" => "$uid"), "LIMIT 1");
    
    if ($db->num_rows($query) <= 0) {
        return false;        
    }
    $user = $db->fetch($query);
    return $user;
}

function SMBasic_getUserID () { //track used by externa/ news plugin only ATM
    if (S_SESSION_INT("isLogged", 1) == 1) {
        return S_SESSION_INT("uid", 11);
    } 
    return false;
}

function SMBasic_sessionToken() {
    return  md5(uniqid(rand(), true));
}