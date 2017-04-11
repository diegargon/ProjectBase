<?php
/*
  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>  
<div class="featured_wrap <?php !empty($data['numcols_class_extra']) ? print $data['numcols_class_extra'] : false ?>">
    <a href="<?= $data['url']; ?>">
        <section class="featured ">
            <?php
            !empty($data['mainimage']) && empty($data['headlines']) ? print $data['mainimage'] : false;
            !empty($tpldata['news_featured_article_pre']) ? print $tpldata['news_featured_article_pre'] : false;
            ?>
            <p class="p-extra-small"><?= $data['date'] ?></p>
            <h1><?= $data['title'] ?></h1>            
            <article>                
                <?php empty($data['headlines']) ? print "<h3>" . $data['lead'] . "</h3>" : false ?>
                <?php !empty($tpldata['news_featured_article_after']) ? print $tpldata['news_featured_article_after'] : false; ?>
            </article>
        </section>
    </a>
</div>
