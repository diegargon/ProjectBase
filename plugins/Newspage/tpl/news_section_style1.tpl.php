<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">
    <?php
    isset($tpldata['ADD_TOP_SECTION']) ? print $tpldata['ADD_TOP_SECTION'] : false;
    isset($data['featured']) ? print $data['featured'] : false;
    if ($config['NEWS_SECTION_COLS'] >= 1) {
        ?>
        <section class="col col<?php print $config['NEWS_SECTION_COLS']; ?>">
            <?php isset($data['col1_articles']) ? print $data['col1_articles'] : false; ?>
        </section>
        <?php
    }
    if ($config['NEWS_SECTION_COLS'] >= 2) {
        ?>
        <section class="col col<?php print $config['NEWS_SECTION_COLS']; ?>">
            <?php isset($data['col2_articles']) ? print $data['col2_articles'] : false; ?>
        </section>
        <?php
    }
    if ($config['NEWS_SECTION_COLS'] >= 3) {
        ?>
        <section class="col col<?php print $config['NEWS_SECTION_COLS']; ?>">
            <?php isset($data['col3_articles']) ? print $data['col3_articles'] : false; ?>
        </section>
        <?php
    }
    isset($tpldata['ADD_BOTTOM_SECTION']) ? print $tpldata['ADD_BOTTOM_SECTION'] : false;
    ?>
</div>