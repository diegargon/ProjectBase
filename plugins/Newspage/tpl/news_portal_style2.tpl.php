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
            if (!empty($data['featured'])) {
                ?>
                <div class='featured_container_mod'>
                    <?php
                    print $data['featured'];
                    ?>
                </div>
            <?php
            }
            isset($data['col1_articles']) ? print $data['col1_articles'] : false;
            ?>
        </section>
        <?php
    }
    if ($config['NEWS_PORTAL_COLS'] >= 2) {
        ?>
        <section class="col col<?php print $config['NEWS_PORTAL_COLS'] ?>">
        <?php isset($data['col2_articles']) ? print $data['col2_articles'] : false ?>
        </section>
        <?php
    }
    if ($config['NEWS_PORTAL_COLS'] >= 3) {
        ?>
        <section class="col col<?php print $config['NEWS_PORTAL_COLS'] ?>">
        <?php isset($data['col3_articles']) ? print $data['col3_articles'] : false ?>
        </section>
        <?php
    }
    isset($tpldata['ADD_BOTTOM_NEWS']) ? print $tpldata['ADD_BOTTOM_NEWS'] : false
    ?>
</div>