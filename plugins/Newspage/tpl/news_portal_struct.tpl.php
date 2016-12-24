<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "WebSite",
  "name": "<?php print $config['WEB_NAME'] ?>",
  "alternateName": "<?php print $config['WEB_DESC'] ?>",
  "url": "<?php print $config['WEB_URL'] ?>"
}
</script>
<?php
if (!empty($config['WEB_LOGO'])) {
?>
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "Organization",
  "url": "<?php print $config['WEB_URL'] ?>",
  "logo": "<?php print $config['WEB_LOGO'] ?>" 
}
</script>
<?php } ?>
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "WebSite",
  "url": "<?php print $config['WEB_URL'] ?>",
  "potentialAction": {
  "@type": "SearchAction",
  "target": "<?php print $config['WEB_URL'] ?>search/?q={search_term_string}",
  "query-input": "required name=search_term_string"
  }
}
</script>
