<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SimpleComments_init() { 
    print_debug("SimpleComments initiated", "PLUGIN_LOAD");
    //SC_corefiles();
}

function SC_corefiles() {
    global $tpl;
    includePluginFiles("SimpleComments");
    $tpl->getCSS_filePath("SimpleComments");    
}

function SC_GetComments($plugin, $resource_id, $lang_id = null, $limit = null) {
    global $tpl, $db;
    $content = "";
    $select_ary = array (
        "plugin" => "$plugin", 
        "resource_id" => "$resource_id", 
        "lang_id" => "$lang_id"
    );
    
    if(!empty($limit) && S_VAR_INTEGER($limit)) {
        $LIMIT = "LIMIT " . $limit;
    } else {
        $LIMIT = "";
    }
    
    $query = $db->select_all("comments", $select_ary, "$LIMIT");
    $num_comments = $db->num_rows($query);
    $counter = 0;
    
    while($comment_row = $db->fetch($query)) {
        $counter == 0 ? $comment_row['TPL_FIRST'] = 1 : false;
        $counter == ($num_comments -1 )? $comment_row['TPL_LAST'] = 1 : false;
        $counter++;
        $content .= $tpl->getTPL_file("SimpleComments", "comments", $comment_row);
    }
    return $content;
}

function SC_NewComment($plugin, $resource_id, $lang_id = null) {
    global $tpl;
    $content = "";
    
    $content .= $tpl->getTPL_file("SimpleComments", "new_comment");
    return $content;
}

function SC_AddComment($plugin, $comment, $resource_id, $lang_id = null) {
    global $sm, $db, $LANGDATA;
    
    $user = $sm->getSessionUser();
    if (empty($user)) {
        $user['username'] = $LANGDATA['L_SC_ANONYMOUS'];
        $user['uid'] = -1;
    }
    $new_ary = array (
        "plugin" => "$plugin",
        "resource_id" => "$resource_id",
        "lang_id" => "$lang_id",
        "message" => $db->escape_strip($comment),
        "author" => "{$user['username']}",
        "author_id" => $user['uid']
    );

    $db->insert("comments", $new_ary);
            
}