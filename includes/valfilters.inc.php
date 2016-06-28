<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

//$_GET
function S_GET_INT($var, $max_size = null, $min_size = null) {
    if(empty($_GET[$var])) {
       return false;
    }
    if (!empty($max_size) && (strlen($_GET[$var]) > $max_size)) {
        return false;
    }
    if (!empty($min_size) && (strlen($_GET[$var]) < $min_size)) {
        return false;
    }    
    
    return filter_input(INPUT_GET, $var, FILTER_VALIDATE_INT);
}

function S_GET_CHAR_AZ($var, $max_size = null, $min_size = null) {
    if(empty($_GET[$var])) {
       return false;
    }
    return S_VAR_CHAR_AZ($_GET[$var], $max_size, $min_size);    
}
function S_GET_EMAIL($var) {
    if(empty($_GET[$var])) {
       return false;
    }    
    return filter_input(INPUT_GET, $var, FILTER_VALIDATE_EMAIL);
}
//$_POST
function S_POST_PASSWORD($var, $max_size = null, $min_size = null) {
    if(empty($_POST[$var])) {
       return false;
    }        
    
    return S_VAR_PASSWORD($_POST[$var], $max_size = null, $min_size = null);
}
function S_POST_EMAIL($var) {
    if(empty($_POST[$var])) {
       return false;
    }    
    return filter_input(INPUT_POST, $var, FILTER_VALIDATE_EMAIL);
}
function S_POST_CHAR_AZNUM ($var, $max_size = null, $min_size = null) {
    if(empty($_POST[$var])) {
       return false;
    }    

    return S_VAR_CHAR_AZ_NUM ($_POST[$var], $max_size, $min_size);    
}
function S_POST_CHAR_AZ ($var, $max_size = null, $min_size = null) {
    if(empty($_POST[$var])) {
       return false;
    }    

    return S_VAR_CHAR_AZ($_POST[$var], $max_size, $min_size);    
}
function S_POST_TEXT_UTF8 ($var, $max_size = null, $min_size = null) {
    if(empty($_POST[$var])) {
       return false;
    }    

    return S_VAR_TEXT_UTF8($_POST[$var], $max_size, $min_size);    
}

function S_POST_STRICT_CHARS ($var, $max_size = null, $min_size = null) {
    if(empty($_POST[$var])) {
       return false;
    }    

    return S_VAR_STRICT_CHARS($_POST[$var], $max_size, $min_size);    
}

function S_POST_INT($var, $max_size = null, $min_size = null) {
    if(empty($_POST[$var])) {
       return false;
    }    
    
    return S_VAR_INTEGER($_POST[$var], $max_size, $min_size);    
}

function S_POST_URL($var, $max_size = null, $min_size = null) { 
        
    if(empty($_POST[$var])) {
        return false;
    }
    if(is_array($_POST[$var])) {
        $var_ary = $_POST[$var];
        foreach ($var_ary as $key => $value) {
            $ret = S_VAR_URL($value, $max_size, $min_size);
            if (!$ret) {
                $var_ary[$key] = false;
            }  else {
                $var_ary[$key] = $ret;
            }
        }
        return $var_ary;
    } else {
        return S_VAR_URL($_POST[$var], $max_size, $min_size);
    }
}
//$_SERVER
function S_SERVER_REQUEST_URI() {
    if(empty($_SERVER['REQUEST_URI'])) {
        return false;
    }
    return filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
}
function S_SERVER_USER_AGENT () {
    if(empty($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }
    return filter_input(INPUT_SERVER,'HTTP_USER_AGENT',FILTER_SANITIZE_ENCODED,FILTER_FLAG_STRIP_LOW);
}
function S_SERVER_REMOTE_ADDR () {
    if(empty($_SERVER['REMOTE_ADDR'])) {
        return false;
    }
    return filter_input(INPUT_SERVER, 'REMOTE_ADDR',FILTER_VALIDATE_IP);
}
function S_SERVER_URL($var) {
    if(empty($_SERVER[$var])) {
        return false;
    }
    return S_VAR_URL($_SERVER[$var]);
}
//VAR
function S_VAR_PASSWORD($var, $max_size = null, $min_size = null) {
    global $config;
    if(defined('SM') && empty ($max_size) && empty($min_size)) {
        $max_size = $config['sm_max_password'];
        $min_size = $config['sm_min_password'];
    }
    if (!empty($max_size) && (strlen($var) > $max_size) ) {        
        return false;
    }
    if (!empty($min_size) && (strlen($var) < $min_size) ) {
        return false;
    }
/*    
    No spaces only... allow all characteres since we hash we not need restrict characters
    No keywords requirements, since its more secure and easy remember 
    something like this_is_my_long_password than $12#45ab
 */
    if (!preg_match("/^(\S+)+$/", $var)) {
        return false;
    }
    return $var;
}
function S_VAR_INTEGER($var, $max_size = null, $min_size = null) {
    
    if(empty($var)) {
        return false;
    }
    if (!empty($max_size) && (strlen($var) > $max_size) ) {        
        return false;
    }
    if (!empty($min_size) && (strlen($var) < $min_size) ) {
        return false;
    }
    
    return filter_var($var, FILTER_VALIDATE_INT);    
}
function S_VAR_CHAR_AZ ($var, $max_size = null, $min_size = null) {
    if(empty($var)) {
        return false;        
    }
    if (!empty($max_size) && (strlen($var) > $max_size) ) {
        return false;
    }
    if (!empty($min_size) && (strlen($var) < $min_size)) {
        return false;
    }
    if (preg_match("/[^A-Za-z]/", $var)) {
        return false;
    }
    
    return $var;

}

function S_VAR_URL($var, $max_size = null, $min_size = null) {
    global $config;
    
    if(empty($var)) {
       return false;        
    }
    if ( (strpos($var, 'http://') !== 0) && (strpos($var, 'https://') !== 0)) { 
        $var = "http://" . $var;
    }    
    
    if (!empty($max_size) && (strlen($var) > $max_size) ) {
        return false;
    }
    if (!empty($min_size) && (strlen($var) < $min_size)) {
        return false;
    }

    $url = filter_var($var, FILTER_SANITIZE_URL);  
    $url = filter_var($url, FILTER_VALIDATE_URL);
    
    if ($config['REMOTE_CHECKS'] && (!remote_check($url)) ) {
        $url = false;
    }    
    return $url;
}

function S_VAR_STRICT_CHARS ($var, $max_size = null, $min_size = null) {
    /*
     * This filter  allow: characters Az 1-9 , "_" (in middle) ... Can't begin with number
     * For username, ACL roles    
     * TODO add support for áÁ 
     */
    if(empty($var)) {
        return false;        
    }
    if (!empty($max_size) && (strlen($var) > $max_size) ) {
        return false;
    }
    if (!empty($min_size) && (strlen($var) < $min_size) ) {
        return false;
    }         
    
    if (!preg_match("/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/", $var)) {         
        return false;
    }    
    
    return $var;
}

function S_VAR_TEXT_UTF8 ($var, $max_size = null, $min_size = null) {
    if(empty($var)) {
        return false;        
    }
    if (!empty($max_size) && (strlen($var) > $max_size) ) {
        return false;
    }
    if (!empty($min_size) && (strlen($var) < $min_size) ) {
        return false;
    }  
    //  UTF-8 
    if (!preg_match("//u", $var)) {       
        return false;
    }
    
    return $var;

}

function S_VAR_CHAR_AZ_NUM ($var, $max_size = null, $min_size = null) {
    if(empty($var)) {
        return false;        
    }
    if (!empty($max_size) && (strlen($var) > $max_size)  ) {
        return false;
    }
    if (!empty($min_size) && (strlen($var) < $min_size) ) {
        return false;
    }
    if (!preg_match('/^[A-Za-z0-9]+$/', $var)) {
        return false;
    }
    
    return $var;
}

//SESSION
function S_SESSION_INT($var, $max_size = null, $min_size = null) {
    if (empty($_SESSION[$var])) { 
        return false;
    }
    return S_VAR_INTEGER($_SESSION[$var], $max_size, $min_size);
}
function S_SESSION_CHAR_AZ($var, $max_size = null, $min_size = null) {
    if (empty($_SESSION[$var])) { 
        return false;
    }    
    return S_VAR_CHAR_AZ($_SESSION[$var], $max_size, $min_size);
}
function S_SESSION_CHAR_AZNUM($var, $max_size = null, $min_size = null) {
    if (empty($_SESSION[$var])) { 
        return false;
    }    
    return S_VAR_CHAR_AZ_NUM ($_SESSION[$var], $max_size, $min_size);
}
//COOKIE
function S_COOKIE_INT($var, $max_size = null, $min_size = null) {
    
    if (empty($_COOKIE[$var])) { 
        return false;
    }
    return S_VAR_INTEGER($_COOKIE[$var], $max_size, $min_size);
}
function S_COOKIE_CHAR_AZNUM($var, $max_size = null, $min_size = null) {    
    if (empty($_COOKIE[$var])) { 
        return false;
    }    
    return S_VAR_CHAR_AZ_NUM ($_COOKIE[$var], $max_size, $min_size);
}