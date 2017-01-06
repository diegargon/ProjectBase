<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

//HEAD MOD
$config['PAGE_TITLE'] = $config['WEB_NAME'] . ": " . $LANGDATA['L_WEBINF_ABOUTUS'];
$config['PAGE_DESC'] = $config['WEB_NAME'] . ": " . $LANGDATA['L_WEBINF_ABOUTUS'];
//END HEAD MOD

do_action("common_web_structure");

if ($config['WEBINFO_SHOWEMAIL_ABOUTUS']) {
    $aboutus['CONTACT'] = $LANGDATA['L_WEBINF_CONTACT'] = $LANGDATA['L_WEBINF_CONTACT'] . ": " . $config['CONTACT_EMAIL'];
} else {
    $aboutus['CONTACT'] = $LANGDATA['L_WEBINF_CONTACT'] = $LANGDATA['L_WEBINF_CONTACT'] . ": " . $LANGDATA['L_WEBINFO_ABOUTUS_CONTACT'];
}
$tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("WebInfo", "aboutus", $aboutus));
