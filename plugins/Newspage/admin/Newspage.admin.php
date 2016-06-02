<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Newspage_AdminInit() {
    register_action("add_admin_menu", "Newspage_AdminMenu", "5"); 
}

function Newspage_AdminMenu($params) {
    $tab_num = 101; //TODO: A WAY TO ASSIGN UNIQ NUMBERS
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_content", "Newspage_AdminContent");        
        return "<li class='tab_active'><a href='?admtab=$tab_num'>Newspage</a></li>";
    } else {
        return "<li><a href='?admtab=$tab_num'>Newspage</a></li>";
    }
}

function Newspage_AdminContent($params) {
   global $tpldata, $LANGDATA;    
   
   includePluginFiles("Newspage", 1);
    $tpldata['ADM_ASIDE_OPTION'] = "<li><a href='?admtab=" . $params['admtab'] ."&opt=1'>". $LANGDATA['L_PL_STATE'] ."</a></li>\n";
    $tpldata['ADM_ASIDE_OPTION'] .=  "<li><a href='?admtab=" . $params['admtab'] ."&opt=2'>". $LANGDATA['L_NEWS_MODERATION'] ."</a></li>\n";
    $tpldata['ADM_ASIDE_OPTION'] .= do_action("ADD_ADM_GENERAL_OPT");
    
    $opt = S_GET_INT("opt");
    if ( $opt == 1 || $opt == false) {
        $tpldata['ADM_CONTENT_DESC'] = $LANGDATA['L_GENERAL'] .": ".  $LANGDATA['L_PL_STATE'];
        $tpldata['ADM_CONTENT'] = Admin_GetPluginState("Newspage");
        $tpldata['ADM_CONTENT'] .= "<hr/><p><pre>" . htmlentities(Admin_GetPluginConfigFiles("Newspage")) . "</pre></p>";        
    } else if ($opt == 2) {
        $tpldata['ADM_CONTENT_DESC'] = $LANGDATA['L_GENERAL'] .": ". $LANGDATA['L_NEWS_MODERATION'] ."";
        $tpldata['ADM_CONTENT'] = $LANGDATA['L_NEWS_MODERATION_DESC'];
        $tpldata['ADM_CONTENT'] = Newspage_AdminModeration();                
    }
    
    return getTPL_file("Admin", "admin_std_content");
}

function Newspage_AdminModeration() {
    global $config, $LANGDATA;
    
    $content = "<div>";
    $q = "SELECT * FROM {$config['DB_PREFIX']}news WHERE moderation = '1' LIMIT {$config['NEWS_NUM_LIST_MOD']}";
    $query = db_query($q);

    if (db_num_rows($query) > 0) {
        while ($news_row = db_fetch($query)) {
            $content .= "<p>"
                    . "[<a href=''>{$LANGDATA['L_NEWS_DELETE']}</a>]"
                    . "[<a href=''>{$LANGDATA['L_NEWS_APPROVED']}</a>]"                        
                    . "<a href='/newspage.php?nid={$news_row['nid']}&admin=1&newslang={$news_row['lang']}' target='_blank'>{$news_row['title']}</a>"
                    . "</p>";
        }
    }
    $content .= "</div>";
    
    return $content;
}