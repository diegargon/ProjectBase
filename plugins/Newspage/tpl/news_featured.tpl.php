<?php
/*
 Copyright @ 2016 Diego Garcia
*/
if (!defined('IN_WEB')) { exit; }
?>
<div id="featured_wrap">
    <span class="featured_category"><?php print $LANGDATA['L_NEWS_FEATURED'] ?>:
    <?php isset($data['category']) ? print $data['category'] :false ?>
    </span>
<a href="<?php print $data['url'];?>">
    <section id="featured">
        <?php
        !empty($data['mainimage']) ? print  $data['mainimage'] : false;
        !empty($tpldata['news_featured_article_pre']) ? print $tpldata['news_featured_article_pre'] : false;
        ?>
        <h1><?php echo $data['title']?></h1>
    <article>
            <p class="p-extra-small"><?php print $data['date']?></p>
            <h3><?php echo $data['lead'] ?></h3>
        <?php !empty($tpldata['news_featured_article_after']) ? print $tpldata['news_featured_article_after'] : false; ?>
    </article>
</section>
</a>
</div>