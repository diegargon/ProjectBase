<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

//HEAD MOD
$cfg['PAGE_TITLE'] = $cfg['WEB_NAME'] . ": " . $LNG['L_WEBINF_ADVERTISE'];
$cfg['PAGE_DESC'] = $cfg['WEB_NAME'] . ": " . $LNG['L_WEBINF_ADVERTISE'];
//END HEAD MOD

do_action("common_web_structure");

$tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("WebInfo", "advertise"));
