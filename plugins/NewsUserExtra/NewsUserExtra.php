<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsUserExtra_init() {
    global $config;
    print_debug("NewsUserExtra initiated", "PLUGIN_LOAD");

    includePluginFiles("NewsUserExtra");
    if ($config['NEWXTRA_ALLOW_DISPLAY_REALNAME']) {
        register_action("profile_xtra_show", "news_xtr_profile_show");
        register_action("news_show_page", "NewsXtra_Modify_N_DisplayName");
        register_action("Newspage_get_comments", "NewsXtra_Modify_C_DisplayName", 6);
    }
    //$tpl->getCSS_filePath("NewsUserExtra");
    //$tpl->getCSS_filePath("NewsUserExtra", "NewsUserExtra-mobile");
}

function news_xtr_profile_show($uxExtra) {
    global $tpl;
    $tpl->addto_tplvar("SMBXTRA_PROFILE_FIELDS_BOTTOM", $tpl->getTPL_file("NewsUserExtra", "NewsXtraProfileField", $uxExtra));
    register_action("SMBXtra_ProfileChange", "NewsXtra_ProfileChange");
}

function NewsXtra_ProfileChange(& $set_ary) {
    S_POST_INT("realname_display", 1, 1) ? $set_ary['realname_display'] = 1 : $set_ary['realname_display'] = 0;
}

function NewsXtra_Modify_N_DisplayName(& $news_data) {
    global $config;
    $authorEx_data = uXtra_get($news_data['author_id']);
    if ($authorEx_data['realname_display']) {
        $news_data['author'] = $authorEx_data['realname'];
        $config['PAGE_AUTHOR'] = $authorEx_data['realname'];
    }

    if (!empty($news_data['translator'])) {
        $transEx_data = uXtra_get($news_data['translator_id']);
        if ($transEx_data['realname_display']) {
            $news_data['translator'] = "<a rel='nofollow' href='/{$config['WEB_LANG']}/profile&viewprofile={$transEx_data['uid']}'>{$transEx_data['realname']}</a>";
        }
    }
}

function NewsXtra_Modify_C_DisplayName(& $comment) {
    $userEx_data = uXtra_get($comment['uid']);
    if ($userEx_data['realname_display']) {
        $comment['username'] = $userEx_data['realname'];
    }
}
