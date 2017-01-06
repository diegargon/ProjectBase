<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

if ($config['ITS_BOT'] && $config['INCLUDE_DATA_STRUCTURE']){
?>

<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "Article",
  "headline": "<?php !empty($data['title']) ? print $data['title'] : false ?>",
  "image": {
        "@type": "imageObject",
        "width" : "600",
        "height": "400",
        "url": "<?php !empty($data['ITEM_MAINIMAGE']) ? print $data['ITEM_MAINIMAGE'] : false ?>"
    },
  "datePublished": "<?php !empty($data['ITEM_CREATED']) ? print $data['ITEM_CREATED'] : false ?>",
  "dateModified": "<?php !empty($data['ITEM_MODIFIED']) ? print $data['ITEM_MODIFIED'] : false ?>",
  <?php print $data['ITEM_SECTIONS'] ?>
  "creator": "<?php print $data['author'] ?>",
  "author": "<?php print $data['author'] ?>",
  "articleBody": "<?php print $data['lead'] ?>",
  "publisher": {
    "@type": "Organization",
    "logo": {
      "@type": "ImageObject",
      "url": "<?php print $config['WEB_LOGO'] ?>"
    },
    "name": "<?php print $config['WEB_NAME'] ?>"
    },
  "mainEntityOfPage": "True"
}
</script>
<?php } ?>