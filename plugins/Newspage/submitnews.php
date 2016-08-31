<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

require_once 'includes/news_form.common.php';
require_once 'includes/news_form.submit.php';

if (!empty($_POST['news_submit']) && empty($_POST['preview'])) {
    news_form_submit_process();
} else if (!empty($_POST['preview'])) {
    news_form_preview();
} else {
    do_action("common_web_structure");
    Newspage_FormScript();
    news_new_form();
}