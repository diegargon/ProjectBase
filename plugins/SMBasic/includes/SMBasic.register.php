<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SMBasic_RegisterScript() {
    $script = "";
    if (!check_jsScript("jquery.min.js")) {
        global $external_scripts;
        $external_scripts[] = "jquery.min.js";
        $script .= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\"></script>\n";
    }               
    $script .= getScript_fileCode("SMBasic", "register");
    
    return $script;
}

function SMBasic_Register() {
    global $config, $LANGDATA;
    
    if( ($config['smbasic_need_email'] == 1)  && 
        (($email = S_POST_EMAIL("email")) == false)) {
        $response[] = array("status" => "1", "msg" => $LANGDATA['L_ERROR_EMAIL']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false; 
    }    
    if( ($config['smbasic_need_username'] == 1) && 
        (($username = S_VAR_CHAR_AZ_NUM($_POST['username'], $config['smbasic_max_username'])) == false)) {
        $response[] = array("status" => "2", "msg" => $LANGDATA['L_ERROR_USERNAME']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }
    if( ($config['smbasic_need_username'] == 1) && 
        (strlen($username) < $config['smbasic_min_username']) 
            ) {
        $response[] = array("status" => "2", "msg" => $LANGDATA['L_USERNAME_SHORT'] );    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }    
    if( ($password = s_char($_POST['password'], $config['smbasic_max_password'])) == false ) {  //TODO FILTER
        $response[] = array("status" => "3", "msg" => $LANGDATA['L_ERROR_PASSWORD']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }    
    if (strlen($_POST['password']) < 8) { 
        $response[] = array("status" => "3", "msg" => $LANGDATA['L_ERROR_PASSWORD_MIN']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }    
    $q = "SELECT * FROM {$config['DB_PREFIX']}users WHERE username = '$username'";  //FIX SELECT username or/and mixed with email
    $query = db_query($q);     
    if ((db_num_rows($query)) > 0) {
        $response[] = array("status" => "2", "msg" => $LANGDATA['L_ERROR_USERNAME_EXISTS']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;                
    }
    $q = "SELECT * FROM {$config['DB_PREFIX']}users WHERE email = '$email'";  //FIX SELECT email or/and mixed with email
    $query = db_query($q);    
    if ((db_num_rows($query)) > 0) {
        $response[] = array("status" => "1", "msg" => $LANGDATA['L_ERROR_EMAIL_EXISTS']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;                        
    }        
    
    db_free_result($query);
    
    $password = do_action("encrypt_password", $password);
    if ($config['smbasic_email_confirmation']) {
        $active = mt_rand(11111111, 2147483647); //Largest mysql init
        $register_message = $LANGDATA['L_REGISTER_OKMSG_CONFIRMATION'];
    } else {
        $active = 1;
        $register_message = $LANGDATA['L_REGISTER_OKMSG'];        
    }    
    $mail_msg = SMBasic_create_reg_mail($active);    
    $q = "INSERT INTO {$config['DB_PREFIX']}users ("
        . "username, password, email, active"
        . ") VALUES ("
        . "'$username', '$password', '$email', '$active');";   
    $query = db_query($q);    
    if($query) {       
       mail($email, $LANGDATA['L_REG_EMAIL_SUBJECT'], $mail_msg);       
       $response[] = array("status" => "ok", "msg" => $register_message, "url" => $config['WEB_URL']);       
       echo json_encode($response, JSON_UNESCAPED_SLASHES); 
    } else {
       $response[] = array("status" => "7", "msg" => $LANGDATA['L_REG_ERROR_WHILE']);
       echo json_encode($response, JSON_UNESCAPED_SLASHES);
    }
    
    return true;      
}

function SMBasic_create_reg_mail($active) {
    global $LANGDATA, $config;
    
    if ($active > 1) {        
        $URL = "http://{$_SERVER['HTTP_HOST']}". "/{$config['WEB_LANG']}/". "login.php" . "?active=$active";
        $msg = $LANGDATA['L_REG_EMAIL_MSG_ACTIVE'] . "\n" ."$URL";         
    } else {
        $register_message = $LANGDATA['L_REGISTER_OKMSG'];
        $URL = "http://{$_SERVER['HTTP_HOST']}". "/{$config['WEB_LANG']}/" . "login.php";
        $msg = $LANGDATA['L_REG_EMAIL_MSG_WELCOME'] . "\n" . "$URL";
    }      
    return $msg;
}
