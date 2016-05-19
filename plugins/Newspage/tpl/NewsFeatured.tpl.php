
<!--
 Copyright @ 2016 Diego Garcia
-->
<a href="<?php print $data['URL'];?>">

<section id="featured">
    <h2 class="category_name"><?php print $data['FEATURED_CAT'] ?></h2>
    <article>
        <div class="feature_image">
        <img src="<?php print $data['MEDIA']?>" alt="<?php print $data['TITLE']?>">
        </div>
        <div class="feature_article">
            <p class="p-extra-small"><?php print $data['date']?></p>
            <h2><?php echo $data['TITLE']?></h2>
            <p><?php echo $data['LEAD'] ?></p>
        </div>
    </article>
</section>

</a>