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
    
    print_debug("CheckSession called", "SM_DEBUG");
    $now = time();
    $next_expire = time() + $config['smbasic_session_expire'];
    
    $query = $db->select_all("sessions", array("session_id" => S_SESSION_CHAR_AZNUM("sid"), "session_uid" => S_SESSION_INT("uid")), "LIMIT 1");
    
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
            print_debug("Update session expire at user {$session['session_uid']}", "SM_DEBUG");
            $db->update("sessions", array("session_expire" => "$next_expire"), array("session_uid" => "{$session['session_uid']}"));
        }
    }
    
    return true;
}



function SMBasic_sessionDebugDetails() { 
    global $db;
    
    print_debug("<hr><br/><h2>Session Details</h2>", "SM_DEBUG");
    print_debug("Time Now: ". format_date(time(),true) ."", "SM_DEBUG");
    print_debug("Session VAR ID: {$_SESSION['uid']}", "SM_DEBUG");
    print_debug("Session VAR Username: {$_SESSION['username']}", "SM_DEBUG");
    print_debug("Session VAR SID:  {$_SESSION['sid']}", "SM_DEBUG");
    
    $s_uid = S_SESSION_INT("uid");
    $s_sid = S_SESSION_CHAR_AZNUM("sid");
    $query = $db->select_all("sessions", array("session_uid" =>  "$s_uid", "session_id" => "$s_sid"), "LIMIT 1"); 
    $session = $db->fetch($query);    
    print_debug("Session DB IP: {$session['session_ip']}", "SM_DEBUG");
    print_debug("Session DB Browser: {$session['session_browser']}", "SM_DEBUG");
    print_debug("Session DB Create: {$session['session_created']}", "SM_DEBUG");
    print_debug("Session DB Expire:" . format_date("{$session['session_expire']}", true) ."","SM_DEBUG");
    print_debug("Session DB Admin: {$session['session_admin']} ", "SM_DEBUG");
    print_debug("PHP Session expire: " . ini_get('session.gc_maxlifetime'), "SM_DEBUG");
    print_debug("Cookies State:", "SM_DEBUG");
    if ( isset($_COOKIE) ) {
        print_debug(" is set", "SM_DEBUG");
        print_debug("Cookie Array:", "SM_DEBUG");
        foreach ($_COOKIE as $key=>$val)
        {
            print_debug("Cookie $key -> $val", "SM_DEBUG");
        }   
        print_debug("<hr>", "SM_DEBUG");        
    } else {
        print_debug(" not set", "SM_DEBUG");        
    }
}

function SMBasic_check_IP($db_session_ip) { 
    $ip = S_SERVER_REMOTE_ADDR();
    return ($ip == $db_session_ip) ? true : false;
}

function SMBasic_check_user_agent($db_user_agent) { 
    $user_agent = S_SERVER_USER_AGENT();
    return ($user_agent == $db_user_agent) ? true : false;
}
function SMBasic_create_reg_mail($active) {
    global $LANGDATA, $config;
    
    if ($active > 1) {        
        $URL = "{$config['WEB_URL']}"."login.php" . "?active=$active";
        $msg = $LANGDATA['L_REG_EMAIL_MSG_ACTIVE'] . "\n" ."$URL";         
    } else {        
        $URL = "{$config['WEB_URL']}"."login.php";
        $msg = $LANGDATA['L_REG_EMAIL_MSG_WELCOME'] . "\n" . "$URL";
    }      
    return $msg;
}