<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<a href="<?php echo $data['URL']?>">
<article class="newsbox">
    <p class="p-extra-small"><?php print $data['date']?></p>
    <h3><?php echo $data['TITLE']?></h3>
    <?php echo $data['MEDIA']?>
    <p><?php echo $data['LEAD'] ?></p>
</article>
</a>