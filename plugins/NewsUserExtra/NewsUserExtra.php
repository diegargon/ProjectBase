<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function NewsUserExtra_init() {
    global $cfg;
    print_debug("NewsUserExtra initiated", "PLUGIN_LOAD");

    includePluginFiles("NewsUserExtra");
    if ($cfg['NEWXTRA_ALLOW_DISPLAY_REALNAME']) {
        require_once("plugins/NewsUserExtra/includes/NewsUserExtraDisplayRealname.inc.php");
        register_action("profile_xtra_show", "news_xtr_profile_show");
        register_action("news_show_page", "NewsXtra_Modify_N_DisplayName");
        register_action("Newspage_get_comments", "NewsXtra_Modify_C_DisplayName", 6);
    }
    //$tpl->getCSS_filePath("NewsUserExtra");
    //$tpl->getCSS_filePath("NewsUserExtra", "NewsUserExtra-mobile");
}
