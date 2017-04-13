<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<a href="<?= $data['url']?>">
<article class="newsbox">
    <?php
    if (empty($data['headlines'])) {
        echo "<p class='p-small'>{$data['date']}</p>";
        echo "<h3>{$data['title']} </h3>";
    } else {
        echo "<p class='p-extra-small'>{$data['date']}</p>";
        echo "<h4>{$data['title']} </h4>";
    }
    if (!isset($data['headlines'])) {
        !empty($tpldata['news_preview_lead_pre']) ? print $tpldata['news_preview_lead_pre'] : null;
        (!empty($data['mainimage'])) ? print $data['mainimage'] : null;
        echo "<p>{$data['lead']}</p>";
        !empty($tpldata['news_preview_lead_after']) ? print $tpldata['news_preview_lead_after'] : null;
    }
    ?>
</article>
</a>