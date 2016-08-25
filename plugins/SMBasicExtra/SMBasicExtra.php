<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SMBasicExtra_init() {
    print_debug("SMBasicExtra initiated", "PLUGIN_LOAD");
    register_action("preload_SMBasic_profile", "SMBasic_Extra_Show");
}

function SMBasic_Extra_Show() {
    global $tpl, $sm;
    includePluginFiles("SMBasicExtra");
    $tpl->getCSS_filePath("SMBasicExtra");
    $tpl->AddScriptFile("SMBasicExtra", "profile_extra", "BOTTOM");
    plugin_start("UserExtra");
    $user = $sm->getSessionUser();
    $userEx_data = uXtra_get($user['uid']);
    $tpl->addto_tplvar("SMBASIC_PROFILE_FIELDS_BOTTOM", $tpl->getTPL_file("SMBasicExtra", "profile_fields", $userEx_data));

    register_action("SMBasic_ProfileChange", "SMB_Ex_ProfileChange");
}

//TO INCLUDE
function SMB_Ex_ProfileChange() {
    global $sm, $db, $config, $LANGDATA;
    $user = $sm->getSessionUser();

    $where_ary['uid'] = $user['uid'];
    $check_ary['uid'] = array("operator" => "<>", "value" => "{$user['uid']}"); //check except own user    
    $set_ary = [];

    plugin_start("UserExtra");

    if ($config['smb_xtr_realname'] && ($realname = S_POST_TEXT_UTF8("realname", 64))) {
        $set_ary_tmp['realname'] = $db->escape_strip($realname);
        if ($config['smb_xtr_realname_checkdup']) {
            if (uXtra_checkdup(array_merge($check_ary, $set_ary_tmp)) || ($sm->getUserByUsername($set_ary_tmp['realname']) != false)) {
                unset($set_ary_tmp);
                die('[{"status": "10", "msg": "' . $LANGDATA['L_SM_REALNAME_INUSE'] . '"}]');
            }
        }
        !empty($set_ary_tmp) ? $set_ary = array_merge($set_ary, $set_ary_tmp) : false;
    }

    if (!empty($set_ary) && !empty($where_ary['uid'])) {
        uXtra_upsert($set_ary, $where_ary);
    }
}
