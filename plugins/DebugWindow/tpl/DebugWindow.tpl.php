<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>


<div class="DebugContainer"><strong>Debug Window</strong>
    <div>
        <ul class="ul_scroll">
            <?php
            foreach ($data as $element) {
                echo  $element['filter'] . ": " . $element['msg'] ."<br/>";
            }            
            ?>
        </ul>
    </div>
</div>

    