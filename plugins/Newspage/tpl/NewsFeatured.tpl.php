
<!--
 Copyright @ 2016 Diego Garcia
-->
<a href="<?php echo $data['URL'];?>">

<section id="featured">
    <article>
        <div class="feature_image">
        <img src="<?php echo $data['MEDIA']?>" alt="<?php echo $data['TITLE']?>">
        </div>
        <div class="feature_article">
            <h1><?php echo $data['TITLE'];?></h1>
            <p><?php echo $data['LEAD'] ?></p>
        </div>
    </article>
</section>

</a>