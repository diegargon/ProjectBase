<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<div id="field_switcher">
    <label for="optional_switcher"><?= $LNG['L_SM_PROFILE_OPTIONAL'] ?></label>
    <input id="optional_switcher" name="optional_switcher" type="checkbox" />
</div>
<div id="optional_profile_fields">
    <?php if ($cfg['smb_xtr_realname']) { ?>
        <dl>
            <dt><label><?= $LNG['L_SM_REALNAME'] ?></label></dt>
            <dd>
                <input class="realname" name="realname" type="text"  value="<?= $data['realname'] ?>" title="" autocomplete="off"/>
            </dd>
        </dl>
        <dl>
            <dt><label><?= $LNG['L_SM_REALNAME_PUBLIC'] ?></label></dt>
            <dd>
                <input <?php !empty($data['realname_public']) ? print "checked" : false; ?>  class="realname_public" name="realname_public" type="checkbox"  value="1" />
            </dd>
        </dl>
    <?php } ?>    
    <dl>
        <dt><label><?= $LNG['L_SM_EMAIL_PUBLIC'] ?></label></dt>
        <dd>
            <input <?php !empty($data['email_public']) ? print "checked" : false; ?>  class="email_public" name="email_public" type="checkbox"  value="1" />
        </dd>
    </dl>       
    <?php if ($cfg['smb_xtr_age']) { ?>
        <dl>
            <dt><label><?= $LNG['L_SM_AGE'] ?></label></dt>
            <dd>
                <input class="age" name="age" type="text" maxlength="2" value="<?php !empty($data['age']) ? print $data['age'] : false; ?>" title="" autocomplete="off"/>
            </dd>
        </dl>
        <dl>
            <dt><label><?= $LNG['L_SM_AGE_PUBLIC'] ?></label></dt>
            <dd>
                <input <?php !empty($data['age_public']) ? print "checked" : false; ?>  class="age_public" name="age_public" type="checkbox"  value="1" />
            </dd>
        </dl>        
    <?php } ?>

    <?php if ($cfg['smb_xtr_aboutme']) { ?>
        <dl>
            <dt><label><?= $LNG['L_SM_ABOUTME'] ?></label></dt>
            <dd>
                <textarea class="aboutme" name="aboutme"><?php !empty($data['aboutme']) ? print $data['aboutme'] : false; ?></textarea>
            </dd>
        </dl>
        <dl>
            <dt><label><?= $LNG['L_SM_ABOUTME_PUBLIC'] ?></label></dt>
            <dd>
                <input <?php !empty($data['aboutme_public']) ? print "checked" : false; ?>  class="aboutme_public" name="aboutme_public" type="checkbox"  value="1" />
            </dd>
        </dl>
        <?php !empty($tpldata['SMBXTRA_PROFILE_FIELDS_BOTTOM']) ? print $tpldata['SMBXTRA_PROFILE_FIELDS_BOTTOM'] : false; ?>    
    <?php } ?>
</div>
