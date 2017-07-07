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
  "name": "<?= $cfg['WEB_NAME'] ?>",
  "alternateName": "<?= $cfg['WEB_DESC'] ?>",
  "url": "<?= $cfg['WEB_URL'] ?>"
}
</script>
<?php
if (!empty($cfg['WEB_LOGO'])) {
?>
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "Organization",
  "url": "<?= $cfg['WEB_URL'] ?>",
  "logo": "<?= $cfg['WEB_LOGO'] ?>" 
}
</script>
<?php } ?>
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "WebSite",
  "url": "<?= $cfg['WEB_URL'] ?>",
  "potentialAction": {
  "@type": "SearchAction",
  "target": "<?= $cfg['WEB_URL'] ?>search/?q={search_term_string}",
  "query-input": "required name=search_term_string"
  }
}
</script>
