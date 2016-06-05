<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<a href="<?php echo $data['URL']?>">
<article class="newsbox">
    <p class="p-extra-small"><?php print $data['date']?></p>
    <?php 
        if (empty($data['headlines'])) {
            echo "<h3>{$data['TITLE']} </h3>";
        } else {
            echo "<h4>{$data['TITLE']} </h4>";            
        }
    ?>
    
    <?php if (!isset($data['headlines'])) {
        echo $data['MEDIA'];
        echo "<p>{$data['LEAD']}</p>";
    } 
    ?>
</article>
</a>