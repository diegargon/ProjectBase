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
  "image": "<?php !empty($data['ITEM_MAINIMAGE']) ? print $data['ITEM_MAINIMAGE'] : false ?>",
  "datePublished": "<?php !empty($data['ITEM_CREATED']) ? print $data['ITEM_CREATED'] : false ?>",
  "articleSection": "entertainment",
  "creator": "<?php print $data['author'] ?>",
  "author": "<?php print $data['author'] ?>",
  "articleBody": "<?php print $data['lead'] ?>",
  "mainEntityOfPage": "True"
}
</script>
<?php } ?>