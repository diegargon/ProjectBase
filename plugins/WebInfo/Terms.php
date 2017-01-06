<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

//HEAD MOD
$config['PAGE_TITLE'] = $config['WEB_NAME'] . ": " . $LANGDATA['L_WEBINF_TOS'];
$config['PAGE_DESC'] = $config['WEB_NAME'] . ": " . $LANGDATA['L_WEBINF_TOS'];
//END HEAD MOD

do_action("common_web_structure");

$tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("WebInfo", "terms"));
