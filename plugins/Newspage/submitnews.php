<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

require_once 'includes/news_form.common.php';
require_once 'includes/news_form.submit.php';

if (!empty($_POST['newsFormSubmit_ST2']) ) {
    news_form_process("admin"); //if author admin options ins't submited
} else if(!empty($_POST['preview'])) {
    news_form_preview();
} else {
    do_action("common_web_structure");
    Newspage_FormScript();
    news_form_new();
}