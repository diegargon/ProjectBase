<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">
    <?php
    isset($tpldata['ADD_TOP_NEWS']) ? print $tpldata['ADD_TOP_NEWS'] : false;
    if (!empty($data['featured'])) {
        ?>
        <div class='featured_container'>
            <?php
            print $data['featured'];
            ?>  
        </div>
        <?php
    }
    if ($config['NEWS_PORTAL_COLS'] >= 1) {
        ?>
        <section class="col col2">
            <?php isset($data['col1_articles']) ? print $data['col1_articles'] : false ?>
        </section>
        <?php
    }
    if ($config['NEWS_PORTAL_COLS'] >= 2) {
        ?>
        <section class="col col2">            
            <?php isset($data['col2_articles']) ? print $data['col2_articles'] : false ?>
        </section>
        <?php
    }
    isset($tpldata['ADD_BOTTOM_NEWS']) ? print $tpldata['ADD_BOTTOM_NEWS'] : false;
    ?>
</div>