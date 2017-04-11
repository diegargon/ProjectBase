<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<dl>
    <dt><label><?= $LANGDATA[$data['profile_title']] ?> </label></dt>
    <dd class="<?php !empty($data['profile_class']) ? print $data['profile_class'] : false; ?>"><span><?= $data['profile_content'] ?> </span></dd>
</dl>