<?php
/*
 Copyright @ 2016 Diego Garcia
*/
?>
<a href="<?php print $data['URL'];?>">
<section id="featured">
    <h2><?php isset($data['CATEGORY']) ? print $data['CATEGORY'] :false ?></h2>
    <article>
        <div class="feature_image">
        <img src="<?php print $data['MEDIA']?>" alt="<?php print $data['TITLE']?>">
        </div>
        <div class="feature_article">
            <p class="p-extra-small"><?php print $data['date']?></p>
            <h3><?php echo $data['TITLE']?></h3>
            <p><?php echo $data['LEAD'] ?></p>
        </div>
    </article>
</section>
</a>