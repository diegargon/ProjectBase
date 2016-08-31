<?php
/*
 Copyright @ 2016 Diego Garcia
*/
!defined('IN_WEB') ? exit : true;
?>  
<div id="featured_wrap">
    <a href="<?php print $data['url']; ?>">
        <section id="featured">
            <?php
            !empty($data['mainimage'] && empty($data['headlines'])) ? print $data['mainimage'] : false;
            !empty($tpldata['news_featured_article_pre']) ? print $tpldata['news_featured_article_pre'] : false;
            ?>
            <h1><?php echo $data['title'] ?></h1>
            <article>
                <p class="p-extra-small"><?php print $data['date'] ?></p>
                <h3><?php empty($data['headlines']) ? print $data['lead'] : false ?></h3>
                <?php !empty($tpldata['news_featured_article_after']) ? print $tpldata['news_featured_article_after'] : false; ?>
            </article>
        </section>
    </a>
</div>
