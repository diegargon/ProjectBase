<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<div id="cat_menu">
    <ul>
        <?php !empty($data['cat_list']) ? print $data['cat_list'] : null; ?>
    </ul>
</div>
<?php if (!empty($data['cat_sub_list'])) { ?>
    <div id="cat_sub_menu">
        <ul>
            <?php !empty($data['cat_sub_list']) ? print $data['cat_sub_list'] : null; ?>
        </ul>
    </div>
<?php } ?>