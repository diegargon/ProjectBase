<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function NewsMediaUploader_init() { 
    global $cfg, $tpl, $sm;
    print_debug("NewsMediaUploader initiated", "PLUGIN_LOAD");

    includePluginFiles("NewsMediaUploader");    
    $tpl->getCSS_filePath("NewsMediaUploader");
    //$tpl->getCSS_filePath("NewsMediaUploader", "NewsMediaUploader-mobile");    
    
    $user = $sm->getSessionUser();
    
    if ($cfg['NMU_ALLOW_ANON'] == 0 && empty($user['uid'])) {     
        $tpl->addto_tplvar("NEWS_FORM_TOP_OPTION", NMU_disable_warn());
        return false;
    }
    
    if ($user && defined('ACL') && $cfg['NMU_ACL_CHECK']) {
        global $acl_auth;
        if ( !$acl_auth->acl_ask($cfg['NMU_ACL_LIST'])) {
            $tpl->addto_tplvar("NEWS_FORM_TOP_OPTION", NMU_disable_warn());
            return false;
        }
    }
    register_action("news_new_form_add", "NMU_form_add");
    register_action("news_edit_form_add", "NMU_form_add");
    register_action("news_newlang_form_add", "NMU_form_add");
    register_action("news_newpage_form_add", "NMU_form_add");    
}

function NMU_form_add($news) {
    global $tpl, $sm, $cfg;

    if (!empty($news['news_auth']) && $news['news_auth'] == "translator") { //translator can upload new files
        return false;
    }
    ($user = $sm->getSessionUser()) ? $extra_content['UPLOAD_EXTRA'] = NMU_upload_list($user) : false;

    $tpl->AddScriptFile("standard", "jquery", "TOP", null);
    $tpl->AddScriptFile("NewsMediaUploader", "plupload.full.min", "TOP", null);
    if ($cfg['NMU_REMOTE_FILE_UPLOAD']) {
        $tpl->addto_tplvar("NEWS_FORM_MIDDLE_OPTION", $tpl->getTPL_file("NewsMediaUploader", "remoteFileUpload", $extra_content));
    }
    $tpl->addto_tplvar("NEWS_FORM_MIDDLE_OPTION", $tpl->getTPL_file("NewsMediaUploader", "formFileUpload", $extra_content));
}

function NMU_upload_list($user) {
    global $db, $cfg;

    $content = "<div id='photobanner'>";
    $select_ary = [
        "plugin" => "news_img_upload", 
        "source_id" => $user['uid'],
    ];
    
    $query = $db->select_all("links", $select_ary, "ORDER BY `date` DESC LIMIT {$cfg['NMU_USER_IMG_LIST_MAX']}");
    while ($link = $db->fetch($query)) {        
        $link_thumb = str_replace("[S]", "/thumbs/", $link['link']);
        $textToadd = "[localimg]" . $link['link']  . "[/localimg]";
        $content .= "<a href=\"#news_text\" onclick=\"addtext('$textToadd'); return false\"><img src='$link_thumb' alt='' /></a>";
    }
    $content .= "</div>";
    return $content;
}

function NMU_disable_warn() {
    global $LNG;
    $content = "<p class='warn_disable'>{$LNG['L_NMU_W_DISABLE']}</p>";
    return $content;
}