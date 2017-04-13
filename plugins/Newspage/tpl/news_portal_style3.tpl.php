<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">
    <?php
    isset($tpldata['ADD_TOP_NEWS']) ? print $tpldata['ADD_TOP_NEWS'] : null;
    if (!empty($data['featured'])) {
        ?>
        <div class='featured_container'>
            <?= $data['featured'] ?>  
        </div>
        <?php
    }
    if ($config['NEWS_PORTAL_COLS'] >= 1) {
        ?>
        <section class="col col2">
            <?= isset($data['col1_articles']) ? $data['col1_articles'] : null ?>
        </section>
        <?php
    }
    if ($config['NEWS_PORTAL_COLS'] >= 2) {
        ?>
        <section class="col col2">            
            <?= isset($data['col2_articles']) ? $data['col2_articles'] : null ?>
        </section>
        <?php
    }
    isset($tpldata['ADD_BOTTOM_NEWS']) ? print $tpldata['ADD_BOTTOM_NEWS'] : null;
    ?>
</div>