<?php
global $tpldata;

?>
<body id="Top">
    <?php 
    if (empty($tpldata['ADD_TO_BODY'])) {
        echo "<p>Hello World</p>";
    } else {
       echo "$tpldata[ADD_TO_BODY]";
    }



