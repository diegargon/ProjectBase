<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
?>
<a href="<?php echo $data['URL']?>">
<article class="newsbox">
    <p class="p-extra-small"><?php print $data['date']?></p>
    <h3><?php echo $data['TITLE']?></h3>
    <img src="<?php echo $data['MEDIA']?>" alt="<?php echo print $data['ALT_TITLE']?>">
    <p><?php echo $data['LEAD'] ?></p>
</article>
</a>
