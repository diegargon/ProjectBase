<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">   
    <div class="standard_box">
        <h1><?= $data['BOX_TITLE'] ?></h1> 
        <p class="p_center_big"> 
            <?= $data['BOX_MSG'] ?>                
        </p>
        <p class="p_center_medium">
            <a href="<?= $data['BOX_BACKLINK'] ?>">
                <?= $data['BOX_BACKLINK_TITLE'] ?>
            </a>
        </p>           
    </div>
</div>