<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsFormExtraMediaTpl($news_data) {
    global $tpl;
    $tpl->AddScriptFile("NewsMedia", "addmediafield");
    $tpl->addto_tplvar("NEWS_FORM_MIDDLE_OPTION", $tpl->getTPL_file("NewsMedia", "NewsMediaExtraItems", $news_data)); 
}

function NewsExtraMediaInsertNew ($news_data) {
    global $db;

    if(!empty($news_data['news_extra_media'])) {
        $urls = $news_data['news_extra_media'];
        $plugin = "Newspage";
        $type = "image";
        foreach($urls as $url) {
            $insert_ary = array (
                "source_id" => $news_data['nid'],
                "plugin" => $plugin,
                "type" => $type,
                "link" => $url,
                "itsmain" => 0
            );
            $db->insert("links", $insert_ary);
        }
    }
}

function NewsExtraMediaCheck(&$news_data) { 
   global $config, $LANGDATA;

   $error_msg = "";
    if(!empty($_POST['news_new_extra_media'])) {
        $urls = $_POST['news_new_extra_media'];
        $first = 1;
        foreach ($urls as $url) {
            if( !empty($url)) {
                $link = S_VALIDATE_MEDIA($url, $config['NEWS_MEDIA_MAX_LENGHT'], $config['NEWS_MEDIA_MIN_LENGHT']);
                if ($link == -1 && $first) {
                     $error_msg .= $LANGDATA['L_NEWS_E_MEDIA_LINK'];
                     $first = 0;
                }
                ($link == -1) ? $error_msg .= "\n" . $url : false;
                (!empty($link) && $link != -1) ? $news_data['news_new_extra_media'][] = $url : false;
            }
        }
    }
    if(!empty($_POST['news_extra_media'])) {
        $urls = $_POST['news_extra_media'];
        $first = 1;
        foreach ($urls as $key => $url) {
            if( !empty($url)) {
                $link = S_VALIDATE_MEDIA($url, $config['NEWS_MEDIA_MAX_LENGHT'], $config['NEWS_MEDIA_MIN_LENGHT']);
                if ($link == -1 && $first) {
                     $error_msg .= $LANGDATA['L_NEWS_E_MEDIA_LINK'];
                     $first = 0;
                }
                ($link == -1) ? $error_msg .= "\n" . $url : false;
                (!empty($link) && $link != -1) ? $news_data['news_extra_media'][$key] = $url : false;
            }
        }
    }
    return (!empty($error_msg)) ? $error_msg : false;
}

function NewsExtraMediaUpdate($news_data) {
    global $db;

    $plugin = "Newspage";
    $type = "image";

    if(!empty($news_data['news_extra_media'])) {
        $media_links = $news_data['news_extra_media'];
        foreach($media_links as $key => $link) {
            $link_ids[] = $key;
            $db->update("links", array("link" => $link), array("link_id" => $key));
        }
        //DELETE links not posted
        $select_ary = array(
            "plugin" => $plugin,
            "source_id" => $news_data['nid'],
            "type" => $type,
            "itsmain" => array("operator" => "!=", "value" => 1)
        );
        $query = $db->select_all("links", $select_ary);

        while ($link_row = $db->fetch($query)) {
            $found_link_id = 0;
            foreach($link_ids as $link_id) {
                ($link_row['link_id'] == $link_id) ? $found_link_id = 1: false;
            }
            ($found_link_id == 0) ? $db->delete("links", array("link_id" => $link_row['link_id'])) : false;
        }
    } else {
        $db->delete("links", array("source_id" => $news_data['nid'], "type" => "image", "itsmain" => array("operator" => "!=", "value" => 1)));
    }

    if(!empty($news_data['news_new_extra_media'])) {
        $media_links = $news_data['news_new_extra_media'];
        foreach($media_links as $link) {
            $insert_ary = array (
                "source_id" => $news_data['nid'],
                "plugin" => $plugin,
                "type" => $type,
                "link" => $link,
                "itsmain" => 0
            );
        $db->insert("links", $insert_ary);
        }
    }
}

function NewsEditExtraFormMediaTpl($news_data) {
    global $tpl;

    if($news_data['news_auth'] == "translator") {
        return false;
    }

    $extra_ary = array(
       "itsmain" => array("operator" => "!=", "value" => "1")
    );

    $links = get_links($news_data['nid'], "image", $extra_ary);

    if(!empty($links)) {
        $news_data['extra_media'] = "";
        $counter = 1;
        foreach($links as $link) {
            $news_data['extra_media'] .= "<div class='wrapper' id='submited_field". $counter++ . "'>";
            $news_data['extra_media'] .= "<input type=\"text\" class=\"news_extra_link\" value=\"{$link['link']}\" name=\"news_extra_media[{$link['link_id']}]\"/>";
            $news_data['extra_media'] .=  "<input type=\"button\" onclick='removeParent(this)' value=\"-\" />";
            $news_data['extra_media'] .= "</div>";
        }
    }
    $tpl->AddScriptFile("NewsMedia", "addmediafield");
    $tpl->addto_tplvar("NEWS_FORM_MIDDLE_OPTION", $tpl->getTPL_file("NewsMedia", "NewsMediaExtraItems", $news_data));    
}
