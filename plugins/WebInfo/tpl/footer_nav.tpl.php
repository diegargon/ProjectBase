<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<div class="footer_menu">
    <div class="footer_menu_inner">
        <ul>
            <?php isset($data['footer_menu']) ? print $data['footer_menu'] : false; ?>
        </ul>
    </div>
</div>