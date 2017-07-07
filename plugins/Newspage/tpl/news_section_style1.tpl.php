<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">
    <?php
    isset($tpldata['ADD_TOP_SECTION']) ? print $tpldata['ADD_TOP_SECTION'] : false;
    if ($cfg['NEWS_SECTION_COLS'] >= 1) {
        ?>
        <section class="col col<?= $cfg['NEWS_SECTION_COLS']; ?>">
            <div class='featured_container_section'> <?= isset($data['featured']) ? $data['featured'] : null ?> </div>
            <?= isset($data['col1_articles']) ? $data['col1_articles'] : null ?>
        </section>
        <?php
    }
    if ($cfg['NEWS_SECTION_COLS'] >= 2) {
        ?>
        <section class="col col<?= $cfg['NEWS_SECTION_COLS']; ?>">
            <?= isset($data['col2_articles']) ? $data['col2_articles'] : null ?>
        </section>
        <?php
    }
    if ($cfg['NEWS_SECTION_COLS'] >= 3) {
        ?>
        <section class="col col<?= $cfg['NEWS_SECTION_COLS']; ?>">
            <?= isset($data['col3_articles']) ? $data['col3_articles'] : null ?>
        </section>
        <?php
    }
    if ($cfg['NEWS_SECTION_COLS'] >= 4) {
        ?>
        <section class="col col<?= $cfg['NEWS_SECTION_COLS']; ?>">
            <?= isset($data['col4_articles']) ? $data['col4_articles'] : nill ?>
        </section>
    <?php
    }
    isset($tpldata['ADD_BOTTOM_SECTION']) ? print $tpldata['ADD_BOTTOM_SECTION'] : null;
    ?>
</div>