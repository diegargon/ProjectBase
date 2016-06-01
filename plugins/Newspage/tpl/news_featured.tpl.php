<?php
/*
 Copyright @ 2016 Diego Garcia
*/
if (!defined('IN_WEB')) { exit; }
?>
<a href="<?php print $data['URL'];?>">
<section id="featured">
    <h2><?php isset($data['CATEGORY']) ? print $data['CATEGORY'] :false ?></h2>
    <article>
<?php if (!empty($data['MEDIA'])) {
?>        
        <div class="feature_image"><?php print $data['MEDIA']?></div>
<?php } ?>        
        <div class="feature_article">
            <p class="p-extra-small"><?php print $data['date']?></p>
            <h3><?php echo $data['TITLE']?></h3>
            <p><?php echo $data['LEAD'] ?></p>
        </div>
    </article>
</section>
</a>