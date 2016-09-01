<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">
    <?php
    isset($tpldata['ADD_TOP_NEWS']) ? print $tpldata['ADD_TOP_NEWS'] : false;
    if ($config['NEWS_PORTAL_COLS'] >= 1) {
        ?>
        <section class="col col<?php print $config['NEWS_PORTAL_COLS'] ?>">
            <?php
            isset($tpldata['FEATURED']) ? print $tpldata['FEATURED'] : false;
            isset($tpldata['COL1_ARTICLES']) ? print $tpldata['COL1_ARTICLES'] : false;
            ?>
        </section>
        <?php
    }
    if ($config['NEWS_PORTAL_COLS'] >= 2) {
        ?>
        <section class="col col<?php print $config['NEWS_PORTAL_COLS'] ?>">
            <?php isset($tpldata['COL2_ARTICLES']) ? print $tpldata['COL2_ARTICLES'] : false ?>
        </section>
        <?php
    }
    if ($config['NEWS_PORTAL_COLS'] >= 3) {
        ?>
        <section class="col col<?php print $config['NEWS_PORTAL_COLS'] ?>">
            <?php isset($tpldata['COL3_ARTICLES']) ? print $tpldata['COL3_ARTICLES'] : false ?>
        </section>
        <?php
    }
    isset($tpldata['ADD_BOTTOM_NEWS']) ? print $tpldata['ADD_BOTTOM_NEWS'] : false
    ?>
</div>