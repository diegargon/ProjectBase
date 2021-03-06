<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SMB_Ex_ProfileChange() {
    global $sm, $db, $cfg, $LNG, $UXtra;
    $user = $sm->getSessionUser();

    $where_ary['uid'] = $user['uid'];
    $check_ary['uid'] = array("operator" => "<>", "value" => "{$user['uid']}"); //check except own user    
    $set_ary = [];

    if ($cfg['smb_xtr_realname'] && ($realname = S_POST_TEXT_UTF8("realname", 64))) {
        $set_ary_tmp['realname'] = trim($db->escape_strip($realname));

        if ($cfg['smb_xtr_realname_checkdup']) {
            if ($UXtra->checkdup(array_merge($check_ary, $set_ary_tmp)) || ($sm->getUserByUsername($set_ary_tmp['realname']) != false)) {
                die('[{"status": "10", "msg": "' . $LNG['L_SM_REALNAME_INUSE'] . '"}]');
            }
        }
        if (preg_match('/[0-9]/', $set_ary_tmp['realname'])) {
            die('[{"status": "11", "msg": "' . $LNG['L_SM_REALNAME_E_NONUM'] . '"}]');
        }

        if (strpos($set_ary_tmp['realname'], " ") < $cfg['smb_xtr_realname_snames']) {
            die('[{"status": "12", "msg": "' . $LNG['L_SM_REALNAME_E_SNAME'] . '"}]');
        }
        !empty($set_ary_tmp) ? $set_ary = array_merge($set_ary, $set_ary_tmp) : false;
    }
    if (($age = S_POST_INT("age"))) {
        (strlen($age) <= 2) && (strlen($age) > 0) ? $set_ary['age'] = trim($age) : die('[{"status": "13", "msg": "' . $LNG['L_SM_E_AGE'] . '"}]');
    }
    if (($aboutme = S_POST_TEXT_UTF8("aboutme"))) {
        if (mb_strlen($aboutme, $cfg['CHARSET']) > $cfg['smb_xtr_aboutme_maxchar']) {
            die('[{"status": "14", "msg": "' . $LNG['L_SM_E_ABOUTME_MAX'] . '"}]');
        } else {
            $set_ary['aboutme'] = $db->escape_strip($aboutme);
        }
    }
    S_POST_INT("realname_public", 1, 1) ? $set_ary['realname_public'] = 1 : $set_ary['realname_public'] = 0;
    S_POST_INT("email_public", 1, 1) ? $set_ary['email_public'] = 1 : $set_ary['email_public'] = 0;
    S_POST_INT("age_public", 1, 1) ? $set_ary['age_public'] = 1 : $set_ary['age_public'] = 0;
    S_POST_INT("aboutme_public", 1, 1) ? $set_ary['aboutme_public'] = 1 : $set_ary['aboutme_public'] = 0;

    do_action("SMBXtra_ProfileChange", $set_ary);

    if (!empty($set_ary) && !empty($where_ary['uid'])) {
        $UXtra->upsert($set_ary, $where_ary);
    }
}
