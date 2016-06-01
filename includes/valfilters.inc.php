<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function s_char($char, $size) {
    print_debug("Deprecated: schar $char");
    if (strlen($char) <= $size) {
        return input_filter($char);
    } else if ($size == 0) { // 0 disable size
        return input_filter($char); 
    }
    return false;
}

function s_num($num, $size) {
    print_debug("Deprecated: snum $num use S_VAR_INTEGER");
    if(is_numeric($num) && (strlen($num) <= $size)) {
        return $num;
    }
    return false;
}

function s_bool($bool) {
    return filter_var($bool, FILTER_VALIDATE_BOOLEAN);
}

function input_filter($data) {
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

function S_VAR_TEXT ($var, $max_size = null, $min_size = null) {
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
    
    return htmlspecialchars($var);
}