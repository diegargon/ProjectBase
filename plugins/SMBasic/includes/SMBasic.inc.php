<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */

function SMBasic_encrypt_password($password) {
    global $config;
    
    if($config['smbasic_use_salt']) {
        return hash('sha512', md5($password . $config['smbasic_salt'] ));
    } else {
        return hash('sha512', $password);      
    }
}