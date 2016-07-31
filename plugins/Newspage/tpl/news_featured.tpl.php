<?php
/*
 Copyright @ 2016 Diego Garcia
*/
if (!defined('IN_WEB')) { exit; }
?>
<div class="category">
    <?php isset($data['category']) ? print $data['category'] :false ?>
</div>
<a href="<?php print $data['url'];?>">
    <section id="featured">
    <div class="feature_article">
        <?php !empty($tpldata['news_featured_article_pre']) ? print $tpldata['news_featured_article_pre'] : false; ?>
        <h1><?php echo $data['title']?></h1>
    <article>
            <p class="p-extra-small"><?php print $data['date']?></p>
            <h2><?php echo $data['lead'] ?></h2>

        <?php !empty($tpldata['news_featured_article_after']) ? print $tpldata['news_featured_article_after'] : false; ?>
    </article>
    </div>
</section>
</a>