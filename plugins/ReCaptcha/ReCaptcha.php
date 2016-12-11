<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function ReCaptcha_init() {
    global $tpl;
    print_debug("ReCaptcha initiated", "PLUGIN_LOAD");

    includePluginFiles("ReCaptcha");
    $tpl->addStdScript("recaptcha", "https://www.google.com/recaptcha/api.js");
    $tpl->AddScriptFile("standard", "recaptcha", "BOTTOM");
    //$tpl->getCSS_filePath("Template");
    $tpl->getCSS_filePath("ReCaptcha", "ReCaptcha-mobile");
    $script = "<script>function rc_cb() {";
    $script .= "document.getElementById('btnSend').disabled = false;";
    $script .= "}</script>";
    $tpl->addto_tplvar("SCRIPTS_BOTTOM", $script);
}

function captcha_validator($response) {
    global $config;
    $url = $config['RC_VERIFY_URL'];
    $data['secret'] = $config['RC_PRIVATE_KEY'];
    $data['response'] = $response;

    $options = array(
        'http' => array(//http work with https url
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        return false;
    } else {
        $result_json = json_decode($result);
        if (($result_json->success) != 1) {
            return false;
        } else {
            return true;
        }
    }
}
