<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

do_action("common_web_structure");
if ($config['WEBINFO_SHOWEMAIL_ABOUTUS']) {
    $aboutus['CONTACT'] = $LANGDATA['L_WEBINF_CONTACT'] = "Contacto: " . $config['CONTACT_EMAIL'];
} else {
    $aboutus['CONTACT'] = $LANGDATA['L_WEBINF_CONTACT'] = "Contacto: " . $LANGDATA['L_WEBINFO_ABOUTUS_CONTACT'];
}
$tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("WebInfo", "aboutus", $aboutus));