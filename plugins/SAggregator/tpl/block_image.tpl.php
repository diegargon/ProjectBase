<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */

?>
<div class="aggre_block">
    <div class="block_image">
        <a  href="<?= $data['url'] ?>" target="_blank"><img src="<?= $data['url'] ?>" alt="" /></a>
    </div>
    <div class="block_title">
        <a href="<?= $data['url']?>" target="_blank"><?= $data['title']?></a>
    </div>
    <div class="image_details">
        <span><?= $data['timeDiff'] ?></span>
        <span><?= $LNG['BY'] ?>&nbsp;</span>
        <a href="" class=""><?= $data['authorName'] ?></a>
        <span><?= $LNG['IN'] ?>&nbsp;</span>
        <a href="" class="" ><?= $data['catName'] ?></a>
    </div>    
    <div class="image_options">
        <?php if ($cfg['SAGGRED_ALLOW_COMM']) { ?>
        <a href=""><?=  $data['NUM_COMM'] . "&nbsp;" . $LNG['COMMENTS'] ?></a>
        <?php } ?>
    </div>
</div>

