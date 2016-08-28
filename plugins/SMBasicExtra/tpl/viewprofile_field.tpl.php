<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>

<dl>
    <dt><label><?php print $LANGDATA[$data['profile_title']] ?> </label></dt>
    <dd><span><?php print $data['profile_content'] ?> </span></dd>
</dl>