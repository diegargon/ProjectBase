<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_xtr_profile_show($uxExtra) {
    global $tpl;
    $tpl->addto_tplvar("SMBXTRA_PROFILE_FIELDS_BOTTOM", $tpl->getTPL_file("NewsUserExtra", "NewsXtraProfileField", $uxExtra));
    register_action("SMBXtra_ProfileChange", "NewsXtra_ProfileChange");
}

function NewsXtra_ProfileChange(& $set_ary) {
    S_POST_INT("realname_display", 1, 1) ? $set_ary['realname_display'] = 1 : $set_ary['realname_display'] = 0;
}

function NewsXtra_Modify_N_DisplayName(& $news_data) {
    global $cfg, $UXtra;
    $authorEx_data = $UXtra->getById($news_data['author_id']);
    if ($authorEx_data['realname_display']) {
        $news_data['author'] = $authorEx_data['realname'];
        $cfg['PAGE_AUTHOR'] = $authorEx_data['realname'];
    }

    if (!empty($news_data['translator_id'])) {
        $transEx_data = $UXtra->getById($news_data['translator_id']);
        if ($transEx_data['realname_display']) {
            $news_data['translator'] = "<a rel='nofollow' href='/{$cfg['WEB_LANG']}/profile&viewprofile={$transEx_data['uid']}'>{$transEx_data['realname']}</a>";
        }
    }
}

function NewsXtra_Modify_C_DisplayName(& $comment) {
    global $UXtra;
    $userEx_data = $UXtra->getById($comment['uid']);
    if ($userEx_data['realname_display']) {
        $comment['username'] = $userEx_data['realname'];
    }
}
