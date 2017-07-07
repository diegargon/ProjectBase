<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

//HEAD MOD
$cfg['PAGE_TITLE'] = $cfg['WEB_NAME'] . ": " . $LNG['L_WEBINF_ABOUTUS'];
$cfg['PAGE_DESC'] = $cfg['WEB_NAME'] . ": " . $LNG['L_WEBINF_ABOUTUS'];
//END HEAD MOD

do_action("common_web_structure");

if ($cfg['WEBINFO_SHOWEMAIL_ABOUTUS']) {
    $aboutus['CONTACT'] = $LNG['L_WEBINF_CONTACT'] = $LNG['L_WEBINF_CONTACT'] . ": " . $cfg['CONTACT_EMAIL'];
} else {
    $aboutus['CONTACT'] = $LNG['L_WEBINF_CONTACT'] = $LNG['L_WEBINF_CONTACT'] . ": " . $LNG['L_WEBINFO_ABOUTUS_CONTACT'];
}
$tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("WebInfo", "aboutus", $aboutus));
