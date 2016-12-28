<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

class SessionManager {

    private $db = null;
    private $user;
    private $users_cache_db = [];

    /*
     * 1 php default php 2 custom 
     */
    private $session_type;
    private $session_start;
    private $session_expire = 86400;
    private $cookie_prefix;
    private $cookie_expire = 86400;
    private $persistence = 0;
    private $salt;

    /*
     * Custom session data array
     */
    private $s_data = [];

    /* */

    private $check_ip;
    private $check_user_agent;
            
    function __construct() {
        
    }

    function start($config, $db) {
        $this->setConfig($config, $db);
        $this->session_start ? session_start() : false;
    }

    function getUserbyID($uid) {

        if (isset($this->users_cache_db[$uid])) {
            return $this->users_cache_db[$uid];
        }

        $query = $this->db->select_all("users", array("uid" => $uid), "LIMIT 1");
        if ($this->db->num_rows($query) <= 0) {
            return false;
        }
        $user = $this->db->fetch($query);
        $this->users_cache_db[$user['uid']] = $user;

        return $user;
    }

    function getUserByUsername($username) {

        if (($uid = array_search($username, array_column($this->users_cache_db, 'username')))) {
            return $this->users_cache_db[$uid];
        }
        $query = $this->db->select_all("users", array("username" => $username), "LIMIT 1");

        if ($this->db->num_rows($query) <= 0) {
            return false;
        }
        $user = $this->db->fetch($query);
        $this->users_cache_db[$user['uid']] = $user;

        return $user;
    }

    function getSessionUser() {
        return $this->user;
    }

    function checkSession() {

        print_debug("CheckSession called", "SM_DEBUG");

        if ($this->checkAnonSession() && empty($this->getData("oauth_token"))) {
            print_debug("SMBasic User: checkSession its setting to anonymous, stopping more checks", "SM_DEBUG");
            return true;
        }

        if ($this->session_type == 1) {
            return $this->check_phpbuildin_session();
        } else {
            die("Custom session Not work/tested yet");
            return $this->check_custom_session();
        }
    }

    function getAllUsersArray($order_field = "regdate", $order = "ASC", $limit = 20) {

        $extra = "ORDER BY " . $order_field . " " . $order . " LIMIT " . $limit;
        $query = $this->db->select_all("users", null, $extra);
        while ($user_row = $this->db->fetch($query)) {
            $users_ary[] = $user_row;
        }

        return $users_ary;
    }

    function searchUser($string, $email = false, $glob = false) {

        $where_ary = [];

        if (!empty($email)) {
            if (empty($glob)) {
                $where_ary = array("email" => array("value" => "'" . $string . "'", "operator" => "LIKE"));
            } else {
                $where_ary = array("email" => array("value" => "'%" . $string . "%'", "operator" => "LIKE"));
            }
        } else {
            if (empty($glob)) {
                $where_ary = array("username" => array("value" => "'" . $string . "'", "operator" => "LIKE"));
            } else {
                $where_ary = array("username" => array("value" => "'%" . $string . "%'", "operator" => "LIKE"));
            }
        }
        $query = $this->db->select_all("users", $where_ary);
        if ($this->db->num_rows($query) > 0) {
            while ($user_row = $this->db->fetch($query)) {
                $users_ary[] = $user_row;
            }
            return $users_ary;
        }

        return false;
    }

    function setData($key, $value) {

        $this->session_type == 1 ? $_SESSION[$key] = $value : false;

        if ($this->session_type == 2) {
            $this->s_data[$key] = $value;
            $this->saveData();
        }
    }

    function getData($key) {
        if ($this->session_type == 1 && isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        if ($this->session_type == 2 && isset($this->s_data[$key])) {
            return $this->s_data[$key];
        }
    }

    function unsetData($key) {
        if ($this->session_type == 1 && isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
        if ($this->session_type == 2 && isset($this->s_data[$key])) {
            unset($this->s_data[$key]);
            //TODO update session data table field
        }
    }

    function destroyData() {
        $this->session_type == 1 ? $_SESSION = [] : false;

        if ($this->session_type == 2) {
            $this->s_data = [];
            //TODO clear session table data field
        }
    }

    private function saveData() {
        $data = serialize($this->s_data);
        $next_expire = time() + $this->session_expire;
        $this->db->update("sessions", array("session_data", "$data", "session_expire", $next_expire), array("uid", $this->user['uid']), "LIMIT 1");
    }
    private function loadData() {
        $query = $this->db->select_all("sessions", array("uid", $this->user['uid']), "LIMIT 1");
        $session = $this->db->fetch($query);

        if ($session['session_expire'] < time()) {
            return false; //session expire
        } else if ( !empty($session['session_data']) ){
            $this->s_data = unserialize($session['session_data']);
        }
    }
    function setUserSession($user, $remember = 0) {

        print_debug("SMBasic: setUserSession called ", "SM_DEBUG");
        $this->unsetAnonSession();

        //TODO PHP7 supports change session expire? DOIT, <7 will destroy and use default 20m
        $session_expire = time() + $this->session_expire;

        if ($this->session_type == 1) {
            $this->setData("uid", $user['uid']);
            session_regenerate_id(true);
            $sid = session_id();
        } else { //Custom
            $sid = $this->createSID();
        }

        if($this->check_ip) {
            $this->setData("session_ip", S_SERVER_REMOTE_ADDR());
        }

        if($this->check_user_agent) {
            $this->setData("session_user_agent", S_SERVER_USER_AGENT());
        }
        
        if (!($this->session_type == 1) || ($this->persistence && $remember)) {
            $ip = $this->db->escape_strip(S_SERVER_REMOTE_ADDR());
            $user_agent = $this->db->escape_strip(S_SERVER_USER_AGENT());

            $this->db->delete("sessions", array("session_uid" => "{$user['uid']}"), "LIMIT 1");

            $q_ary = [
                "session_id" => $sid,
                "session_uid" => $user['uid'],
                "session_ip" => "$ip",
                "session_browser" => "$user_agent",
                "session_expire" => $session_expire
            ];

            $this->db->insert("sessions", $q_ary);
            $this->setCookies($sid, $user['uid'], $remember);
            $this->db->update("users", array("last_login" => date("Y-m-d H:i:s", time())), array("uid" => $user['uid']));
        }
        
        
        return $sid;
    }

    function destroy() {
        print_debug("SMBasic: Session destroy called ", "SM_DEBUG");
        $this->user = false;
        $this->db->delete("sessions", array("session_uid" => $this->user['uid']));
        $this->clearCookies();
        isset($_SESSION) ? session_destroy() : false;
        $this->destroyData();
    }

    function setAnonSession() {

        if ($this->session_type == 1) {
            print_debug("SMBasic: Setting session as anonymous ", "SM_DEBUG");
            $this->clearCookies();
            $this->destroyData();
            $this->setData("anonymous", 1);
        } else {
            print_debug("SMBasic: Setting cookies as anonymous ", "SM_DEBUG");
            $this->clearCookies();
            $cookie_name_anon = $this->cookie_prefix . "anonymous";
            setcookie($cookie_name_anon, 1, 0, '/');
        }
    }

    function regenerate_sid($remember) {

        if (!($user = $this->getSessionUser())) {
            return false;
        }
        
        if ($this->session_type == 1) {
            session_regenerate_id(true);
            $sid = session_id();
        } else {
            $sid = $this->createSID();
        }

        print_debug("Regenerate SID ($this->session_type) and Update session expire on user {$user['username']}", "SM_DEBUG");

        $expire = time() + $this->session_expire;


        if ($this->session_type == 2 || ( $this->persistence && $remember)) {
            $this->setCookies($sid, $user['uid'], $remember);
            $this->db->update("sessions", array("session_expire" => $expire, "session_id" => "$sid"), array("session_uid" => $user['uid']), "LIMIT 1");
        }
    }

    private function setConfig($config, $db) {
        
        $this->db = $db;
        if ($config['smbasic_default_session']) {
            $this->session_type = 1;
        } else { //Custom
            $this->session_type = 2;
            $this->loadData();
        }
        if ($config['smbasic_session_start'] || $config['smbasic_default_session']) {
            $this->session_start = 1;
        }
        $this->salt = $config['smbasic_session_salt'];
        $this->check_ip = $config['smbasic_check_ip'];
        $this->check_user_agent = $config['smbasic_check_user_agent'];
        !empty($config['smbasic_session_expire']) ? $this->session_expire = $config['smbasic_session_expire'] : false;
        !empty($config['smbasic_persistence']) ? $this->persistence = $config['smbasic_persistence'] : false;
        !empty($config['smbasic_cookie_prefix']) ? $this->cookie_prefix = $config['smbasic_cookie_prefix'] : false;
    }

    private function createSID() {
        $hash_string = mt_rand(0, mt_getrandmax()) .
                md5(substr(S_SERVER_REMOTE_ADDR(), 0, 5)) .
                $this->salt .
                md5(microtime(true) . time());

        return hash('sha256', $hash_string);
    }

    private function getCookies() {
        $c['uid'] = S_COOKIE_INT($this->cookie_prefix . "uid", 11);
        $c['sid'] = S_COOKIE_CHAR_AZNUM($this->cookie_prefix . "sid", 64);

        return $c;
    }

    private function clearCookies() {

        $cookie_name_anon = $this->cookie_prefix . "anonymous";
        $cookie_name_sid = $this->cookie_prefix . "sid";
        $cookie_name_uid = $this->cookie_prefix . "uid";
        unset($_COOKIE[$cookie_name_sid]);
        unset($_COOKIE[$cookie_name_uid]);
        unset($_COOKIE[$cookie_name_anon]);
        setcookie($cookie_name_sid, 0, time() - 3600, '/');
        setcookie($cookie_name_uid, 0, time() - 3600, '/');
        setcookie($cookie_name_anon, 0, time() - 3600, '/');
        $this->session_type == 1 ? setcookie('phpsessid', 0, time() - 3600) : false;
    }

    private function setCookies($sid, $uid, $remember) {

        $cookie_name_sid = $this->cookie_prefix . "sid";
        $cookie_name_uid = $this->cookie_prefix . "uid";
        if ($remember) {
            $cookie_expire = time() + $this->cookie_expire;
        } else {
            $cookie_expire = 0; //this session only
        }
        setcookie($cookie_name_sid, $sid, $cookie_expire, '/');
        setcookie($cookie_name_uid, $uid, $cookie_expire, '/');
    }

    private function check_IP() {
        $session_ip = $this->getData("session_ip");
        $ip = S_SERVER_REMOTE_ADDR();
        return ($ip == $session_ip) ? true : false;
    }

    private function check_user_agent() {
        $session_user_agent = $this->getData("session_user_agent");
        $user_agent = S_SERVER_USER_AGENT();
        return ($user_agent == $session_user_agent) ? true : false;
    }

    //TODO... do better later 
    private function checkAnonSession() {
        if ($this->session_type == 1) {
            print_debug("SMBasic: Checking if anon (buildin)", "SM_DEBUG");
            return isset($_SESSION['anonymous']) ? true : false;
        } else {
            print_debug("SMBasic: Checking anon (custom/cookies) ", "SM_DEBUG");
            $cookie_name_anon = $this->cookie_prefix . "anonymous";
            return isset($_COOKIE[$cookie_name_anon]) ? true : false;
        }
    }

    private function unsetAnonSession() {

        if ($this->session_type == 1) {
            print_debug("SMBasic: Unsetting anonymous session ", "SM_DEBUG");
            $this->unsetData("anonymous");
        } else {
            print_debug("SMBasic: Unsetting anonymous cookie ", "SM_DEBUG");
            $cookie_name_anon = $this->cookie_prefix . "anonymous";
            unset($_COOKIE[$cookie_name_anon]);
            setcookie($cookie_name_anon, 0, time() - 3600, '/');
        }
    }

    private function check_phpbuildin_session() {

        $uid = $this->getData("uid");

        if( $this->check_ip && ($this->check_IP() == false) ) {
            print_debug("SMBasic:IP validated FALSE", "SM_DEBUG");
            return false;
        }
  
        if( $this->check_user_agent && ($this->check_user_agent() == false) ) {
            print_debug("SMBasic:User agent validated FALSE", "SM_DEBUG");
            return false;
        }
  
        if (empty($uid) && $this->persistence) {
            $cookies = $this->getCookies();
            if (empty($cookies['uid']) || empty($cookies['sid'])) {
                return false;
            } else {
                print_debug("SMBasic: Checking persintence (buildin) ", "SM_DEBUG");
                $session = $this->check_persistence($cookies);
                if ($session) {
                    $this->user = $this->getUserbyID($session['session_uid']);
                    $this->setData("uid", $this->user['uid']);
                    $this->regenerate_sid(1);
                    return true;
                } else {
                    print_debug("SMBasic: Cookies invalidad detectadas", "SM_DEBUG");
                    $this->clearCookies();
                    return false;
                }
            }
        }
        $this->user = $this->getUserbyID($uid);

        return true;
    }

    private function check_custom_session() {

        $uid = $this->getData("uid");
        
        $cookies = $this->getCookies();
        if (empty($cookies['uid']) || empty($cookies['sid'])) {
            return false;
        } 
        
        if($uid != $cookies['uid']) {
            return false;
        }
        if($this->persitence) {
            print_debug("SMBasic: Check persistence(custom)", "SM_DEBUG");
            $session = $this->check_persistence($cookies);
            if ($session) {
                $this->user = $this->getUserbyID($session['session_uid']);
                $this->setData("uid", $this->user['uid']);
                $this->regenerate_sid(1);                
                return true;
            } else {
                return false;
            }
        }
    }

    private function check_persistence($cookies) {
        $sid = $cookies['sid'];
        $uid = $cookies['uid'];

        $query = $this->db->select_all("sessions", array("session_id" => "$sid", "session_uid" => "$uid"), "LIMIT 1");

        if ($this->db->num_rows($query) <= 0) {
            return false;
        }
        $session = $this->db->fetch($query);
        $this->db->free($query);

        if ($this->check_ip == 1 && (!$this->check_IP($session['session_ip']))) {
            print_debug("SMBasic:IP validated FALSE", "SM_DEBUG");
            return false;
        }
        if ($this->check_user_agent == 1 && (!$this->check_user_agent())) {
            print_debug("SMBasic:UserAgent validated FALSE", "SM_DEBUG");
            return false;
        }

        $now = time();
        $next_expire = time() + $this->session_expire;
        if ($session['session_expire'] < $now) {
            print_debug("SMBasic: db session expired", "SM_DEBUG");
            $this->db->delete("sessions", array("session_id" => $session['session_id']), "LIMIT 1");
            return false;
        }
        print_debug("Update session expire at user {$session['session_uid']}", "SM_DEBUG");
        $this->db->update("sessions", array("session_expire" => "$next_expire"), array("session_uid" => "{$session['session_uid']}"));
        //TODO REGENERATE ID
        return $session;
    }
}
