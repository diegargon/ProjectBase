<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
?>
<?php/*TAGS*/ ?>
<meta name="news_keywords" content="<?php !empty($data['tags']) ? print $data['tags'] : false ?> " />
<meta property="og:title" content="<?php print $data['title'] ?>"/>
<meta property="og:url" content="<?php print $data['url'] ?>"/>
<meta property="og:site_name" content="<?php print $data['web_title'] ?>"/>
<?php /* <meta property="og:image" content="" /> */ ?>
<meta property="og:description" content="<?php print $data['lead'] ?> "/>