<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function s_char($char, $size) { //TODO REPLACE ALL REFERENCES AND DELETE THIS FUNCTION
    print_debug("Deprecated: schar $char");
    if (strlen($char) <= $size) {
        return input_filter($char);
    } else if ($size == 0) { // 0 disable size
        return input_filter($char); 
    }
    return false;
}

function s_num($num, $size) { //TODO REPLACE ALL REFERENCES AND DELETE THIS FUNCTION
    print_debug("Deprecated: snum $num use S_VAR_INTEGER");
    if(is_numeric($num) && (strlen($num) <= $size)) {
        return $num;
    }
    return false;
}

function s_bool($bool) { //TODO REPLACE ALL REFERENCES AND DELETE THIS FUNCTION
    return filter_var($bool, FILTER_VALIDATE_BOOLEAN);
}

function input_filter($data) { //TODO REPLACE ALL REFERENCES AND DELETE THIS FUNCTION and do a decent filter function if not already do.
    global $config;
    
    if (is_array($data)) {
        foreach ($data as $key => $element) {
            $data[$key] = input_filter($element);
        }
    } else {
        
        $data = trim(htmlentities(strip_tags($data)));        
        if(get_magic_quotes_gpc()) $data = stripslashes($data);
        
        if (isset($config['SQL_DB']) && $config['SQL_DB']) {
            $data = db_escape_string($data);
        }
        
    }
    
    return $data;
}

//$_GET

function S_GET_INT($var) {
    if(empty($_GET[$var])) {
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
    if (!empty($max_size)) {
        if (strlen($var) > $max_size) {
            return false;
        }
    }
    if (!empty($min_size)) {
        if (strlen($var) < $min_size) {
            return false;
        }
    }
    
    return filter_var($var, FILTER_VALIDATE_INT);    
}
function S_VAR_CHAR_AZ ($var, $max_size = null, $min_size = null) {
    if(empty($var)) {
        return false;        
    }
    if (!empty($max_size)) {
        if (strlen($var) > $max_size) {
            return false;
        }
    }
    if (!empty($min_size)) {
        if (strlen($var) < $min_size) {
            return false;
        }
    }
    if (preg_match("/[^A-Za-z]/", $var))
    {
        return false;
    }
    
    return $var;

}
function S_VAR_CHAR_AZ_NUM ($var, $max_size = null, $min_size = null) {
    if(empty($var)) {
        return false;        
    }
    if (!empty($max_size)) {
        if (strlen($var) > $max_size) {
            return false;
        }
    }
    if (!empty($min_size)) {
        if (strlen($var) < $min_size) {
            return false;
        }
    }
    if (preg_match('/[^A-Za-z0-9.#\\-$]/', $var)) {
        return false;
    }
    return $var;

}

function S_VAR_TEXT_ESCAPE ($var, $max_size = null, $min_size = null) {
    if (!empty($max_size)) {
        if (strlen($var) > $max_size) {
            return false;
        }
    }
    if (!empty($min_size)) {
        if (strlen($var) < $min_size) {
            return false;
        }
    }
    
    return db_escape_string($var);
}

function S_VALIDATE_URL($url, $max_size = null, $min_size = null) {
    if (!empty($max_size)) {
        if (strlen($url) > $max_size) {
            return false;
        }
    }
    if (!empty($min_size)) {
        if (strlen($url) < $min_size) {
            return false;
        }
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
        return false;
    }    
    if ( !preg_match($regex, $url) ) {
      return false;
    }  
    if ($config['REMOTE_CHECKS']) {
        $headers = get_headers($url);
        if($headers['0'] == 'HTTP/1.1 404 Not Found') {
            return false;
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