<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<div  class="clear bodysize page">
    <div class="profile_box">
        <h1><?php print $LANGDATA['L_VIEWPROFILE'] ?></h1>
        <div id="avatar">
            <?php
            if (!empty($data['avatar'])) {
                ?>
                <img class="image_link" width="125" height="150" src="<?php print $data['avatar'] ?>" alt="" />
                <?php
            } else {
                ?>
                <img width="125" height="150" src="<?php print $config['SMB_IMG_DFLT_AVATAR'] ?>" alt="" />
            <?php } ?>
        </div>
        <div id="profile_fields">
            <dl>
                <dt><label><?php print $LANGDATA['L_USERNAME'] ?> </label></dt>
                <dd><span><?php print $data['username'] ?> </span></dd>
            </dl>
            <dl>
                <dt><label><?php print $LANGDATA['L_SM_REGISTERED'] ?> </span></dt>
                <dd><span><?php print format_date($data['regdate']) ?> </span></dd>
            </dl>
            <dl>
                <dt><label><?php print $LANGDATA['L_SM_LASTLOGIN'] ?> </span></dt>
                <dd><span><?php print format_date($data['last_login']) ?> </span></dd>
            </dl>
            <?php !empty($tpldata['SMB_VIEWPROFILE_FIELDS_BOTTOM']) ? print $tpldata['SMB_VIEWPROFILE_FIELDS_BOTTOM'] : false; ?>            
        </div>
        <p class='p_center_medium'><a href="<?php print $config['BACKLINK'] ?>"><?php print $LANGDATA['L_BACK'] ?></a></p>
    </div>
</div>