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
                echo "INVALID!<br>";
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
//VAR
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
    if(empty($var)) {
       return false;        
    }
    if (!empty($max_size) && (strlen($var) > $max_size) ) {
        return false;
    }
    if (!empty($min_size) && (strlen($var) < $min_size)) {
        return false;
    }
    //TODO REMOTE CHECK VALIDATOR
    $url = filter_var($var, FILTER_SANITIZE_URL);  
    return filter_var($url, FILTER_VALIDATE_URL);
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

function S_VALIDATE_URL($url, $max_size = null, $min_size = null) {
    if (!empty($max_size) && (strlen($url) > $max_size) ) {
        return false;
    }
    if (!empty($min_size) && (strlen($url) < $min_size) ) {
        return false;
    }
    $url = filter_var($url, FILTER_SANITIZE_URL);

    if ( filter_var($url, FILTER_VALIDATE_URL) === false) {
        return false;
    } else {
        return $url;
    }        
}

function S_VALIDATE_MEDIA($url, $max_size = null, $min_size = null) {
    global $config;
    //TODO make something good and optional better remote connection for check
    //TODO add http if not provided
    $regex = '/\.('. $config['ACCEPTED_MEDIA_REGEX'] .')(?:[\?\#].*)?$/';
    
    if( ($url = S_VALIDATE_URL($url, $max_size, $min_size)) == false ) {
        return -1;
    }    
    if ( !preg_match($regex, $url) ) {
      return -1;
    }  
    if ($config['REMOTE_CHECKS']) {
        $headers = get_headers($url);
        if($headers['0'] == 'HTTP/1.1 404 Not Found') {
            return -1;
        }
    }
 
    return $url;
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