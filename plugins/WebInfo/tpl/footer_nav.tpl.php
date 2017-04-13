<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<div class="footer_menu">
    <div class="footer_menu_inner">
        <ul>
            <?= isset($data['footer_menu']) ? $data['footer_menu'] : null ?>
        </ul>
    </div>
</div>