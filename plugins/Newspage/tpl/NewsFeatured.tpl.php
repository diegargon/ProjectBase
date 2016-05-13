
<!--
 Copyright @ 2016 Diego Garcia
-->
<a href="<?php echo $data['URL'];?>">

<section id="featured">
    <h2>Destacada</h2>
    <article>
        <div class="feature_image">
        <img src="<?php echo $data['MEDIA']?>" alt="<?php echo $data['TITLE']?>">
        </div>
        <div class="feature_article">
            <h2><?php echo $data['TITLE'];?></h2>
            <p><?php echo $data['LEAD'] ?></p>
        </div>
    </article>
</section>

</a>