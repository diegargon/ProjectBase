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
            <dt><label><?php print $LANGDATA['L_SM_REALNAME'] ?></dt>
            <dd>
                <input class="realname" name="realname" type="text"  value="<?php print $data['realname'] ?>" title="" autocomplete="off"/>
            </dd>
        </dl>
    <?php } ?>
</div>
