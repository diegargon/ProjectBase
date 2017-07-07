<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

if ($cfg['WEBINFO_RECAPTCHA']) {
    plugin_start("ReCaptcha");
}

//HEAD MOD
$cfg['PAGE_TITLE'] = $cfg['WEB_NAME'] . ": " . $LNG['L_WEBINF_CONTACT'];
$cfg['PAGE_DESC'] = $cfg['WEB_NAME'] . ": " . $LNG['L_WEBINF_CONTACT'];
//END HEAD MOD

if (!empty($_POST)) {
    $mail['email'] = S_POST_EMAIL("email");
    empty($mail['email']) ? die('{"status": "1", "msg": "' . $LNG['L_CONTACT_E_EMAIL'] . '"}') : false;
    $mail['name'] = S_POST_CHAR_AZ("name", 32);
    empty($mail['name']) ? die('{"status": "2", "msg": "' . $LNG['L_CONTACT_E_NAME'] . '"}') : false;
    $mail['subject'] = S_POST_TEXT_UTF8("subject", 256);
    empty($mail['subject']) ? die('{"status": "3", "msg": "' . $LNG['L_CONTACT_E_SUBJECT'] . '"}') : false;
    $mail['message'] = S_POST_TEXT_UTF8("message", 500);
    empty($mail['message']) ? die('{"status": "4", "msg": "' . $LNG['L_CONTACT_E_MESSAGE'] . '"}') : false;
    if ($cfg['WEBINFO_RECAPTCHA']) {

        $captcha = S_POST_TEXT_UTF8("g-recaptcha-response");
        empty($captcha) ? die('{"status": "6", "msg": "' . $LNG['L_CONTACT_E_CAPTCHA'] . '"}') : false;
        if (($captcha_resp = captcha_validator($captcha)) == false) {
            empty($captcha) ? die('{"status": "6", "msg": "' . $LNG['L_CONTACT_E_CAPTCHA_NOPASS'] . '"}') : false;
        }
    }
    $mail['to'] = $cfg['CONTACT_EMAIL'];

    if (ContactSendMail($mail) == 0) {
        die('{"status": "ok", "msg": "' . $LNG['L_CONTACT_MAIL_SUCCESS'] . '"}');
    }
} else {
    do_action("common_web_structure");
    $tpl->AddScriptFile("WebInfo", "contactform", "TOP", "");
    $tpl->AddScriptFile("standard", "jquery");
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("WebInfo", "contact"));
}

function ContactSendMail($mail) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: {$mail['email']}" . "\r\n";
    $headers .= "Reply-To:  {$mail['email']}" . "\r\n";
    $headers .= "X-Mailer: PHP" . "\r\n";
    mail($mail['to'], $mail['subject'], $mail['message'], $headers);
    return 0;
}
