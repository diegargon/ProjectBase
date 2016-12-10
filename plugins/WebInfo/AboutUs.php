<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

do_action("common_web_structure");
$tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("WebInfo", "aboutus"));