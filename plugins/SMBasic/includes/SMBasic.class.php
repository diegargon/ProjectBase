<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

class SessionManager {

    private $user;
    private $users_cache_db = [];

    function __construct() {
        global $config;
        $config['smbasic_default_session'] || $config['smbasic_session_start'] ? session_start() : false;
    }

    function getUserbyID($uid) {
        global $db;

        if (isset($this->users_cache_db[$uid])) {
            return $this->users_cache_db[$uid];
        }

        $query = $db->select_all("users", array("uid" => "$uid"), "LIMIT 1");
        if ($db->num_rows($query) <= 0) {
            return false;
        }
        $user = $db->fetch($query);
        $this->users_cache_db[$user['uid']] = $user;

        return $user;
    }

    function getUserByUsername($username) {
        global $db;

        if (($uid = array_search($username, array_column($this->users_cache_db, 'username')))) {
            return $this->users_cache_db[$uid];
        }

        $query = $db->select_all("users", array("username" => $username), "LIMIT 1");

        if ($db->num_rows($query) <= 0) {
            return false;
        }
        $user = $db->fetch($query);
        $this->users_cache_db[$user['uid']] = $user;

        return $user;
    }

    function getSessionUser() {
        return $this->user;
    }

    function getSessionSID() {
        global $config;

        if ($config['smbasic_default_session']) {
            $sid = S_SESSION_CHAR_AZNUM("sid");
        } else {
            $cookies = $this->getCookies();
            $sid = $cookies['sid'];
        }

        return $sid;
    }

    function createSID() {
        return md5(uniqid(rand(), true));
    }

    function checkSession() {
        global $config;

        print_debug("CheckSession called", "SM_DEBUG");

        if ($this->checkAnonSession() && empty($_SESSION['oauth_token'])) {
            print_debug("SMBasiUser: checkSession its setting to anonymous, stopping more checks", "SM_DEBUG");
            return true;
        }

        if ($config['smbasic_oauth'] && !empty($_SESSION['oauth_token'])) {
            return $this->check_oauth_session();
        }
        if ($config['smbasic_default_session']) {
            return $this->check_phpbuildin_session();
        } else {
            return $this->check_custom_session();
        }
    }

    private function check_oauth_session() {

        if (empty($this->user)) {
            if (!S_SESSION_INT("uid")) {
                return false;
            } else {
                $this->user = $this->getUserbyID(S_SESSION_INT("uid"));
            }
        }
        return true;
    }

    function setUserSession($user, $remember = 0) {
        global $config, $db;

        print_debug("SMBasic: setUserSession called ", "SM_DEBUG");
        $this->unsetAnonSession();
        $sid = $this->createSID();

        //TODO PHP7 supports change session expire DOIT, <7 will destroy and use default 20m
        $session_expire = time() + $config['smbasic_session_expire'];

        if ($config['smbasic_default_session']) {
            $_SESSION['uid'] = $user['uid'];
            $_SESSION['sid'] = $sid;
        }

        if (!$config['smbasic_default_session'] || ($config['smbasic_persistence'] && $remember)) {
            $ip = $db->escape_strip(S_SERVER_REMOTE_ADDR());
            $user_agent = $db->escape_strip(S_SERVER_USER_AGENT());

            $db->delete("sessions", array("session_uid" => "{$user['uid']}"), "LIMIT 1");

            $q_ary = array(
                "session_id" => "$sid",
                "session_uid" => "{$user['uid']}",
                "session_ip" => "$ip",
                "session_browser" => "$user_agent",
                "session_expire" => "$session_expire"
            );

            $db->insert("sessions", $q_ary);
            $this->setCookies($sid, $user['uid'], $remember);
            $db->update("users", array("last_login" => date("Y-m-d H:i:s", time())), array("uid" => $user['uid']));
        }

        return $sid;
    }

    function getCookies() {
        global $config;

        $cookies['uid'] = S_COOKIE_INT("{$config['smbasic_cookie_prefix']}uid", 11);
        $cookies['sid'] = S_COOKIE_CHAR_AZNUM("{$config['smbasic_cookie_prefix']}sid", 32);

        return $cookies;
    }

    function destroy() {
        global $db;

        print_debug("SMBasic: Session destroy called ", "SM_DEBUG");
        $this->user = false;
        $db->delete("sessions", array("session_uid" => $this->user['uid']));
        $this->clearCookies();
        isset($_SESSION) ? session_destroy() : false;
        $_SESSION = [];
    }

    function clearCookies() {
        global $config;

        $cookie_name_anon = $config['smbasic_cookie_prefix'] . "anonymous";
        $cookie_name_sid = $config['smbasic_cookie_prefix'] . "sid";
        $cookie_name_uid = $config['smbasic_cookie_prefix'] . "uid";
        unset($_COOKIE[$cookie_name_sid]);
        unset($_COOKIE[$cookie_name_uid]);
        unset($_COOKIE[$cookie_name_anon]);
        setcookie($cookie_name_sid, 0, time() - 3600, '/');
        setcookie($cookie_name_uid, 0, time() - 3600, '/');
        setcookie($cookie_name_anon, 0, time() - 3600, '/');
        $config['smbasic_default_session'] || $config['smbasic_session_start'] ? setcookie('phpsessid', 0, time() - 3600) : false;
    }

    function setCookies($sid, $uid, $remember) {
        global $config;

        $cookie_name_sid = $config['smbasic_cookie_prefix'] . "sid";
        $cookie_name_uid = $config['smbasic_cookie_prefix'] . "uid";
        if ($remember) {
            $cookie_expire = time() + $config['smbasic_cookie_expire'];
        } else {
            $cookie_expire = 0; //this session only
        }
        setcookie($cookie_name_sid, $sid, $cookie_expire, '/');
        setcookie($cookie_name_uid, $uid, $cookie_expire, '/');
    }

    function checkCookies() {
        global $db;

        $cookies = $this->getCookies();
        if (!$cookies['uid'] || !$cookies['sid']) {
            return false;
        }

        $query = $db->select_all("sessions", array("session_id" => "{$cookies['sid']}", "session_uid" => "{$cookies['uid']}"), "LIMIT 1");
        if ($db->num_rows($query) <= 0) {
            $this->destroy();
            return false;
        }

        if (($user = $this->getUserbyID($cookies['uid'])) != false) {
            $this->setUserSession($user);
            $this->setCookies(S_SESSION_CHAR_AZNUM("sid", 32), S_SESSION_INT("uid", 11)); //New sid by setSession -> new cookies
            return true;
        }
    }

    function check_IP($db_session_ip) {
        $ip = S_SERVER_REMOTE_ADDR();
        return ($ip == $db_session_ip) ? true : false;
    }

    function check_user_agent($db_user_agent) {
        $user_agent = S_SERVER_USER_AGENT();
        return ($user_agent == $db_user_agent) ? true : false;
    }

    //TODO... do better later 
    function checkAnonSession() {
        global $config;

        if ($config['smbasic_default_session']) {
            print_debug("SMBasic: Checking if anon (buildin)", "SM_DEBUG");
            return isset($_SESSION['anonymous']) ? true : false;
        } else {
            print_debug("SMBasic: Checking anon (custom/cookies) ", "SM_DEBUG");
            $cookie_name_anon = $config['smbasic_cookie_prefix'] . "anonymous";
            return isset($_COOKIE[$cookie_name_anon]) ? true : false;
        }
    }

    function setAnonSession() {
        global $config;

        if ($config['smbasic_default_session']) {
            print_debug("SMBasic: Setting session as anonymous ", "SM_DEBUG");
            $this->clearCookies();
            $_SESSION = [];
            $_SESSION['anonymous'] = 1;
        } else {
            print_debug("SMBasic: Setting cookies as anonymous ", "SM_DEBUG");
            $this->clearCookies();
            $cookie_name_anon = $config['smbasic_cookie_prefix'] . "anonymous";
            setcookie($cookie_name_anon, 1, 0, '/');
        }
    }

    function unsetAnonSession() {
        global $config;

        if ($config['smbasic_default_session']) {
            print_debug("SMBasic: Unsetting anonymous session ", "SM_DEBUG");
            unset($_SESSION['anonymous']);
        } else {
            print_debug("SMBasic: Unsetting anonymous cookie ", "SM_DEBUG");
            $cookie_name_anon = $config['smbasic_cookie_prefix'] . "anonymous";
            unset($_COOKIE[$cookie_name_anon]);
            setcookie($cookie_name_anon, 0, time() - 3600, '/');
        }
    }

    function getAllUsersArray($order_field = "regdate", $order = "ASC", $limit = 20) {
        global $db;

        $extra = "ORDER BY " . $order_field . " " . $order . " LIMIT " . $limit;
        $query = $db->select_all("users", null, $extra);
        while ($user_row = $db->fetch($query)) {
            $users_ary[] = $user_row;
        }

        return $users_ary;
    }

    function searchUser($string, $email = false, $glob = false) {
        global $db;
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
        $query = $db->select_all("users", $where_ary);
        if ($db->num_rows($query) > 0) {
            while ($user_row = $db->fetch($query)) {
                $users_ary[] = $user_row;
            }
            return $users_ary;
        }

        return false;
    }

    private function check_phpbuildin_session() {
        global $config;
        $uid = S_SESSION_INT("uid");

        if (empty($uid) && $config['smbasic_persistence']) {
            $cookies = $this->getCookies();
            if (empty($cookies['uid']) || empty($cookies['sid'])) {
                return false;
            } else {
                print_debug("SMBasic: Checking persintence (buildin) ", "SM_DEBUG");
                $session = $this->check_persistence($cookies);
                if ($session) {
                    $this->user = $this->getUserbyID($session['session_uid']);
                    return true;
                } else {
                    return false;
                }
            }
        }
        $this->user = $this->getUserbyID($uid);

        return true;
    }

    private function check_custom_session() {

        $cookies = $this->getCookies();
        if (empty($cookies['uid']) || empty($cookies['sid'])) {
            return false;
        } else {
            print_debug("SMBasic: Check persistence(custom)", "SM_DEBUG");
            $session = $this->check_persistence($cookies);
            if ($session) {
                $this->user = $this->getUserbyID($session['session_uid']);
                return true;
            } else {
                return false;
            }
        }
    }

    private function check_persistence($cookies) {
        global $config, $db;
        $sid = $cookies['sid'];
        $uid = $cookies['uid'];

        $query = $db->select_all("sessions", array("session_id" => "$sid", "session_uid" => "$uid"), "LIMIT 1");

        if ($db->num_rows($query) <= 0) {
            return false;
        }

        $session = $db->fetch($query);
        $db->free($query);

        if (!$this->check_extra($session)) {
            return false;
        }

        $now = time();
        $next_expire = time() + $config['smbasic_session_expire'];
        if ($session['session_expire'] < $now) {
            print_debug("SMBasic: db session expired at $now", "SM_DEBUG");
            $db->delete("sessions", array("session_id" => $session['session_id']), "LIMIT 1");
            return false;
        }
        print_debug("Update session expire at user {$session['session_uid']}", "SM_DEBUG");
        $db->update("sessions", array("session_expire" => "$next_expire"), array("session_uid" => "{$session['session_uid']}"));

        return $session;
    }

    /* regerate must change sid,  on session table and SESSION
      private function regenerate_sid() {
      global $config, $db, $sm;
      if ($config['smbasic_default_session'] || $config['smbasic_session_start']) {
      session_regenerate_id(true);
      }
      if (!$config['smbasic_default_session'] || $config['smbasic_persistence']) {
      if ((!$user = $sm->getSessionUser())) {
      return false;
      }
      $new_sid = $this->createSID();
      $next_expire = time() + $config['smbasic_session_expire'];
      print_debug("Renereate SID and Update session expire on user {$user['username']}", "SM_DEBUG");
      $db->update("sessions", array("session_expire" => "$next_expire", "session_id" => "$new_sid"), array("session_uid" => "{$user['uid']}"), "LIMIT 1");
      }
      }
     */

    private function check_extra($check) {
        global $config;

        if ($config['smbasic_check_ip'] == 1 && (!$this->check_IP($check['session_ip']))) {
            print_debug("SMBasic:IP validated FALSE", "SM_DEBUG");
            return false;
        }
        if ($config['smbasic_check_user_agent'] == 1 && (!$this->check_user_agent($check['session_browser']))) {
            print_debug("SMBasic:UserAgent validated FALSE", "SM_DEBUG");
            return false;
        }

        return true;
    }

}
