<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<?php if ($config['NEWXTRA_ALLOW_DISPLAY_REALNAME']) { ?>
    <dl>
        <dt><label><?= $LANGDATA['NEWS_XTRA_DISPLAY_REALNAME'] ?> </label></dt>
        <dd class=""><input <?php !empty($data['realname_display']) ? print "checked" : false; ?>  class="realname_display" name="realname_display" type="checkbox"  value="1" /></dd>
    </dl>
<?php } ?>
