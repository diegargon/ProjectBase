<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
?>

<div class="DebugContainer"><strong>Debug Window</strong>
    <div>
        <ul class="ul_scroll">
            <?php
            foreach ($data as $element) {
                echo $element . "<br/>";
            }            
            ?>
        </ul>
    </div>
</div>

    