<?php

?>
<section  class="">
<a href="<?php echo $data['URL'];?>">
<article class="newsbox">
    <h2><?php echo $data['TITLE'];?></h2>
    <img src="<?php echo $data['MEDIA']?>" alt="<?php echo text_echo($data['TITLE'])?>">
    <p><?php echo $data['LEAD'] ?></p>
</article>
</a>
</section>