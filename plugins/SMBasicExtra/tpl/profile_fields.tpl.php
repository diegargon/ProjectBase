<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>

<div id="field_switcher">
    <label for="optional_switcher"><?php print $LANGDATA['L_SM_PROFILE_OPTIONAL'] ?></label>
    <input id="optional_switcher" name="optional_switcher" type="checkbox" />
</div>
<div id="optional_profile_fields">
    <?php if ($config['smb_xtr_realname']) { ?>
        <dl>
            <dt><label><?php print $LANGDATA['L_SM_REALNAME'] ?></label></dt>
            <dd>
                <input class="realname" name="realname" type="text"  value="<?php print $data['realname'] ?>" title="" autocomplete="off"/>
            </dd>
        </dl>
        <dl>
            <dt><label><?php print $LANGDATA['L_SM_REALNAME_PUBLIC'] ?></label></dt>
            <dd>
                <input <?php !empty($data['realname_public']) ? print "checked" : false; ?>  class="realname_public" name="realname_public" type="checkbox"  value="1" />
            </dd>
        </dl>
    <?php } ?>    
        <dl>
            <dt><label><?php print $LANGDATA['L_SM_EMAIL_PUBLIC'] ?></label></dt>
            <dd>
                <input <?php !empty($data['email_public']) ? print "checked" : false; ?>  class="email_public" name="email_public" type="checkbox"  value="1" />
            </dd>
        </dl>       

</div>
