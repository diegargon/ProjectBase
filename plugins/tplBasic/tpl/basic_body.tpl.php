<?php
if (!defined('IN_WEB')) { exit; }

global $tpldata;

?>
<body id="Top">
    <?php 
    isset($tpldata['PRE_ACTION_ADD_TO_BODY']) ? print $tpldata['PRE_ACTION_ADD_TO_BODY'] : false;
    
    if (empty($tpldata['ADD_TO_BODY'])) {
        echo "<p>Hello World</p>";
    } else {
       echo "$tpldata[ADD_TO_BODY]";
    }
    isset($tpldata['POST_ACTION_ADD_TO_BODY']) ? print $tpldata['POST_ACTION_ADD_TO_BODY'] : false;



