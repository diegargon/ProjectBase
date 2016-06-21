<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsComments_init() { 
    global $tpl;
    print_debug("NewsComments initiated", "PLUGIN_LOAD");
    

    
    register_action("news_page_begin", "News_Comments");
}

function NC_core_files() {
    includePluginFiles("NewsComments");
    //$tpl->getCSS_filePath("NewsComments");
}
function News_Comments($news) {
    global $config, $tpl;
    
    NC_core_files();

    $nid = $news['nid'];
    $lang_id = $news['lang_id'];
    SC_corefiles();
    $comments = SC_AddComments("Newspage", $nid, $lang_id, $config['NC_MAX_COMMENTS']);
    $new_comment = SC_NewComment("Newspage", $nid, $lang_id);
    $tpl->addto_tplvar("ADD_TO_NEWSSHOW_BOTTOM", $comments . $new_comment);
}
