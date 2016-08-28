<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SMB_Ex_ProfileChange() {
    global $sm, $db, $config, $LANGDATA;
    $user = $sm->getSessionUser();

    $where_ary['uid'] = $user['uid'];
    $check_ary['uid'] = array("operator" => "<>", "value" => "{$user['uid']}"); //check except own user    
    $set_ary = [];

    plugin_start("UserExtra");

    if ($config['smb_xtr_realname'] && ($realname = S_POST_TEXT_UTF8("realname", 64))) {
        $set_ary_tmp['realname'] = trim($db->escape_strip($realname));

        if ($config['smb_xtr_realname_checkdup']) {
            if (uXtra_checkdup(array_merge($check_ary, $set_ary_tmp)) || ($sm->getUserByUsername($set_ary_tmp['realname']) != false)) {
                die('[{"status": "10", "msg": "' . $LANGDATA['L_SM_REALNAME_INUSE'] . '"}]');
            }
        }
        if (preg_match('/[0-9]/', $set_ary_tmp['realname'])) {
            die('[{"status": "11", "msg": "' . $LANGDATA['L_SM_REALNAME_E_NONUM'] . '"}]');
        }

        if (strpos($set_ary_tmp['realname'], " ") < $config['smb_xtr_realname_snames']) {
            die('[{"status": "12", "msg": "' . $LANGDATA['L_SM_REALNAME_E_SNAME'] . '"}]');
        }
        !empty($set_ary_tmp) ? $set_ary = array_merge($set_ary, $set_ary_tmp) : false;
    }

    if (!empty($set_ary) && !empty($where_ary['uid'])) {
        uXtra_upsert($set_ary, $where_ary);
    }
}
