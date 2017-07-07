<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<?php if ($cfg['NEWXTRA_ALLOW_DISPLAY_REALNAME']) { ?>
    <dl>
        <dt><label><?= $LNG['NEWS_XTRA_DISPLAY_REALNAME'] ?> </label></dt>
        <dd class=""><input <?= !empty($data['realname_display']) ? "checked" : null ?>  class="realname_display" name="realname_display" type="checkbox"  value="1" /></dd>
    </dl>
<?php } ?>
