<?php
/*
 Copyright @ 2016 Diego Garcia
*/
if (!defined('IN_WEB')) { exit; }
?>
<a href="<?php print $data['url'];?>">
<section id="featured">
    <h2><?php isset($data['category']) ? print $data['category'] :false ?></h2>
    <article>  
        <?php !empty($tpldata['news_featured_article_pre']) ? print $tpldata['news_featured_article_pre'] : false; ?>
        <div class="feature_article">
            <p class="p-extra-small"><?php print $data['date']?></p>
            <h3><?php echo $data['title']?></h3>
            <p><?php echo $data['lead'] ?></p>
        </div>
        <?php !empty($tpldata['news_featured_article_after']) ? print $tpldata['news_featured_article_after'] : false; ?>
    </article>
</section>
</a>