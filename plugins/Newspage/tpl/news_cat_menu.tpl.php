<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<div id="news_cat_menu">
    <ul>
        <?php
        !empty($tpldata['NEWS_CATMENU_PRE_LIST']) ? print $tpldata['NEWS_CATMENU_PRE_LIST'] : false;
        !empty($data['cat_list']) ? print $data['cat_list'] : null;
        !empty($tpldata['NEWS_CATMENU_POST_LIST']) ? print $tpldata['NEWS_CATMENU_POST_LIST'] : false;
        ?>
    </ul>
</div>
<?php if (!empty($data['cat_sub_list'])) { ?>
    <div id="news_cat_submenu">
        <ul>            
            <?php !empty($data['cat_sub_list']) ? print $data['cat_sub_list'] : null; ?>
        </ul>
    </div>
<?php
}