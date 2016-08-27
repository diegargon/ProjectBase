<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

class SessionManager {

    private $user;

    function __construct() {
        session_start();
    }

    function getUserbyID($uid) {
        global $db;

        $query = $db->select_all("users", array("uid" => "$uid"), "LIMIT 1");

        return ($db->num_rows($query) <= 0) ? false : $db->fetch($query);
    }

    function getUserByUsername($username) {
        global $db;
        $query = $db->select_all("users", array("username" => $username), "LIMIT 1");

        return ($db->num_rows($query) <= 0) ? false : $db->fetch($query);
    }

    function getSessionUser() {
        global $db;

        empty($this->user) ? $this->setSessionUser() : false;
        return $this->user;
    }

    function setSessionUser() {
        $uid = S_SESSION_INT("uid", 11, 1) == false ? $this->user = false : false;
        $query = $db->select_all("users", array("uid" => "$uid"), "LIMIT 1");
        $db->num_rows($query) <= 0 ? $this->user = false : $this->user = $db->fetch($query);
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
                $where_ary = array("email" => array("value" => $string, "operator" => "LIKE"));
            } else {
                $where_ary = array("email" => array("value" => "%" . $string . "%", "operator" => "LIKE"));
            }
        } else {
            if (empty($glob)) {
                $where_ary = array("username" => array("value" => $string, "operator" => "LIKE"));
            } else {
                $where_ary = array("username" => array("value" => "%" . $string . "%", "operator" => "LIKE"));
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

    function destroy() {
        global $db;

        empty($this->user) ? $db->delete("sessions", array("session_uid" => $this->user['uid'])) : false;
        $_SESSION = [];
        session_destroy();
        $this->clearCookies();
    }

    function clearCookies() {
        global $config;

        $cookie_name_sid = $config['smbasic_cookie_prefixname'] . "sid";
        $cookie_name_uid = $config['smbasic_cookie_prefixname'] . "uid";
        unset($_COOKIE[$cookie_name_sid]);
        unset($_COOKIE[$cookie_name_uid]);
        setcookie($cookie_name_sid, 0, time() - 3600, '/');
        setcookie($cookie_name_uid, 0, time() - 3600, '/');
    }

    function sessionToken() {
        return md5(uniqid(rand(), true));
    }

    function setSession($user) {
        global $config, $db;

        $session_expire = time() + $config['smbasic_session_expire'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['uid'] = $user['uid'];
        $_SESSION['sid'] = $this->sessionToken();
        $_SESSION['isLogged'] = 1;
        $ip = $db->escape_strip(S_SERVER_REMOTE_ADDR());
        $user_agent = $db->escape_strip(S_SERVER_USER_AGENT());

        $db->delete("sessions", array("session_uid" => "{$user['uid']}"));

        $q_ary = array(
            "session_id" => "{$_SESSION['sid']}",
            "session_uid" => "{$user['uid']}",
            "session_ip" => "$ip",
            "session_browser" => "$user_agent",
            "session_expire" => "$session_expire"
        );

        $db->insert("sessions", $q_ary);
        $db->update("users", array("last_login" => date("Y-m-d H:i:s", time())), array("uid" => $user['uid']));
    }

    function setCookies($sid, $uid) {
        global $config;
        $cookie_expire = time() + $config['smbasic_cookie_expire'];
        $cookie_name_sid = $config['smbasic_cookie_prefixname'] . "sid";
        $cookie_name_uid = $config['smbasic_cookie_prefixname'] . "uid";
        setcookie($cookie_name_sid, $sid, $cookie_expire, '/');
        setcookie($cookie_name_uid, $uid, $cookie_expire, '/');
    }

    function checkCookies() {
        global $config, $db;

        $cookie_uid = S_COOKIE_INT("{$config['smbasic_cookie_prefixname']}uid", 11);
        $cookie_sid = S_COOKIE_CHAR_AZNUM("{$config['smbasic_cookie_prefixname']}sid", 32);

        if ($cookie_uid == false || $cookie_sid == false) {
            return false;
        }

        $query = $db->select_all("sessions", array("session_id" => "$cookie_sid", "session_uid" => "$cookie_uid"), "LIMIT 1");
        $db->num_rows($query) <= 0 ? $this->destroy() : false;

        if (($user = $this->getUserbyID($cookie_uid)) != false) {
            $this->setSession($user);
            $this->setCookies(S_SESSION_CHAR_AZNUM("sid", 32), S_SESSION_INT("uid", 11)); //New sid by setSession -> new cookies
            return true;
        }
    }

    function checkSession() {
        global $config, $db;

        print_debug("CheckSession called", "SM_DEBUG");
        $now = time();
        $next_expire = time() + $config['smbasic_session_expire'];

        $query = $db->select_all("sessions", array("session_id" => S_SESSION_CHAR_AZNUM("sid"), "session_uid" => S_SESSION_INT("uid")), "LIMIT 1");

        if ($db->num_rows($query) <= 0) {
            return false;
        }
        $session = $db->fetch($query);
        $db->free($query);
        if ($config['smbasic_check_ip'] == 1 && (!$this->check_IP($session['session_ip']))) {
            print_debug("SMBasic:IP validated FALSE", "SM_DEBUG");
            return false;
        } else {
            print_debug("SMBasic:IP validated OK", "SM_DEBUG");
        }
        if ($config['smbasic_check_user_agent'] == 1 && (!$this->check_user_agent($session['session_browser']))) {
            print_debug("SMBasic:UserAgent validated FALSE", "SM_DEBUG");
            return false;
        } else {
            print_debug("SMBasic:UserAgent validated OK", "SM_DEBUG");
        }
        if ($session['session_expire'] < $now) {
            print_debug("SMBasic: db session expired at $now", "SM_DEBUG");
            return false;
        }

        print_debug("Update session expire at user {$session['session_uid']}", "SM_DEBUG");
        $db->update("sessions", array("session_expire" => "$next_expire"), array("session_uid" => "{$session['session_uid']}"));

        return true;
    }

    function check_IP($db_session_ip) {
        $ip = S_SERVER_REMOTE_ADDR();
        return ($ip == $db_session_ip) ? true : false;
    }

    function check_user_agent($db_user_agent) {
        $user_agent = S_SERVER_USER_AGENT();
        return ($user_agent == $db_user_agent) ? true : false;
    }

}
