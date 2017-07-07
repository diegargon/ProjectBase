<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">
    <?php
    isset($tpldata['ADD_TOP_NEWS']) ? print $tpldata['ADD_TOP_NEWS'] : null;
    if ($cfg['NEWS_PORTAL_COLS'] >= 1) {
        ?>
        <section class="col col<?= $cfg['NEWS_PORTAL_COLS'] ?>">
            <?php
            if (!empty($data['featured'])) {
                ?>
                <div class='featured_container_section'>
                    <?php
                    print $data['featured'];
                    ?>
                </div>
            <?php
            }
            isset($data['col1_articles']) ? print $data['col1_articles'] : null;
            ?>
        </section>
        <?php
    }
    if ($cfg['NEWS_PORTAL_COLS'] >= 2) {
        ?>
        <section class="col col<?= $cfg['NEWS_PORTAL_COLS'] ?>">
        <?= isset($data['col2_articles']) ? $data['col2_articles'] : null?>
        </section>
        <?php
    }
    if ($cfg['NEWS_PORTAL_COLS'] >= 3) {
        ?>
        <section class="col col<?= $cfg['NEWS_PORTAL_COLS'] ?>">
        <?= isset($data['col3_articles']) ? $data['col3_articles'] : null ?>
        </section>
        <?php
    }
    if ($cfg['NEWS_PORTAL_COLS'] >= 4) {
        ?>
        <section class="col col<?= $cfg['NEWS_PORTAL_COLS']; ?>">
            <?= isset($data['col3_articles']) ? $data['col3_articles'] : null ?>
        </section>
        <?php
    }        
    isset($tpldata['ADD_BOTTOM_NEWS']) ? print $tpldata['ADD_BOTTOM_NEWS'] : null;
    ?>
</div>