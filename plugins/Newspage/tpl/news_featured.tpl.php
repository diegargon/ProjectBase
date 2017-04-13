<?php
/*
  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>  
<div class="featured_wrap <?= !empty($data['numcols_class_extra']) ? $data['numcols_class_extra'] : null ?>">
    <a href="<?= $data['url']; ?>">
        <section class="featured ">
            <?php
            !empty($data['mainimage']) && empty($data['headlines']) ? print $data['mainimage'] : null;
            !empty($tpldata['news_featured_article_pre']) ? print $tpldata['news_featured_article_pre'] : null;
            ?>
            <p class="p-extra-small"><?= $data['date'] ?></p>
            <h1><?= $data['title'] ?></h1>            
            <article>                
                <?php 
                    empty($data['headlines']) ? print "<h3>" . $data['lead'] . "</h3>" : null;
                   !empty($tpldata['news_featured_article_after']) ? print $tpldata['news_featured_article_after'] : null;
                ?>
            </article>
        </section>
    </a>
</div>
