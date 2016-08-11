<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsMediaUploader_init() { 
    global $config, $tpl, $sm;
    print_debug("NewsMediaUploader initiated", "PLUGIN_LOAD");

    includePluginFiles("NewsMediaUploader");    
    $tpl->getCSS_filePath("NewsMediaUploader");
    //$tpl->getCSS_filePath("NewsMediaUploader", "NewsMediaUploader-mobile");    
    
    $user = $sm->getSessionUser();
    
    if ($config['NMU_ALLOW_ANON'] == 0 && empty($user['uid'])) {        
        return false;
    }
    
    if (defined('ACL') && $config['NMU_ACL_CHECK']) {
        global $acl_auth;
        if ( !$acl_auth->acl_ask($config['NMU_ACL_LIST'])) {
            return false;
        }
    }
    register_action("news_new_form_add", "NMU_form_add");
    register_action("news_edit_form_add", "NMU_form_add");
    register_action("news_newlang_form_add", "NMU_form_add");
    register_action("news_newpage_form_add", "NMU_form_add");    
}

function NMU_form_add () {
    global $tpl, $sm;

    ($user = $sm->getSessionUser()) ? $extra_content['UPLOAD_EXTRA'] = NMU_upload_list($user) : false;

    $tpl->AddScriptFile("standard", "jquery.min", "TOP");
    $tpl->AddScriptFile("NewsMediaUploader", "plupload.full.min", "TOP");
    $tpl->addto_tplvar("NEWS_FORM_MIDDLE_OPTION", $tpl->getTPL_file("NewsMediaUploader", "formFileUpload", $extra_content));
}

function NMU_upload_list($user) {
    global $db, $config;

    $content = "<div id='photobanner'>";
    $query = $db->select_all("links", array("plugin" => "news_img_upload", "source_id" => $user['uid']));
    while ($link = $db->fetch($query)) {
        $link_thumb = $config['IMG_SRV_URL'];
        $link_thumb .= str_replace("[S]", "/thumbs/", $link['link']);
        $textToadd = "[localimg]" . str_replace("[S]", "/desktop/", $link['link'])  . "[/localimg]";
        $content .= "<a href=\"#news_text\" onclick=\"addtext('$textToadd'); return false\"><img src='$link_thumb' alt='' /></a>";
    }
    $content .= "</div>";
    return $content;
}