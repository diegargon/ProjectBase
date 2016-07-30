<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<div  class="clear bodysize page">   
    <div class="profile_box">        
        <form id="profile_form" action="" autocomplete="off" method="post"> 
            <h1><?php print $LANGDATA['L_PROFILE']?></h1>
            <div id="avatar">
<?php
if (!empty($data['avatar'])) {
?>
                <img width="125" height="150" src="<?php print $data['avatar'] ?>" alt="" />
<?php
} else {
?>
                <img width="125" height="150" src="plugins/SMBasic/tpl/img/avatar.png" alt="" />
<?php } ?>
            </div>
            <dl>
                <dt><label><?php print $LANGDATA['L_USERNAME']?></label><br/>
                    <span><?php print $LANGDATA['L_USERNAME_H']?> </span>
                </dt>
                <dd>
<?php
if ($config['smbasic_can_change_username'] && isset($data['username'])) {
?>
                    <input required id="username" name="username" type="text" value="<?php print $data['username']?>" title="<?php print $LANGDATA['L_USERNAME_H']?>" autocomplete="off" />
<?php                
} else if (isset($data['username'])) {
    ?>
                    <input disabled id="username" name="username" type="text" value="<?php print $data['username']?>" title="<?php print $LANGDATA['L_USERNAME_H']?>"/>
<?php
}
?>
                </dd>
            </dl>
            <dl>
                <dt><label>Avatar</dt>
                <dd>
                    <input class="avatar" name="avatar" type="text"  value="<?php print $data['avatar']?>" title="" autocomplete="off"/>
                </dd>
            </dl>
            <dl>
                <dt><label><?php print $LANGDATA['L_EMAIL']?></label><br/>
                    <span><?php print $LANGDATA['L_EMAIL_H']?> </span>
                </dt>
                <dd>
<?php
if ($config['smbasic_can_change_email']) {
?> 
                    <input required id="email" name="email" type="text"  value="<?php print $data['email']?>" title="<?php print $LANGDATA['L_EMAIL_H']?>" autocomplete="off"/>
<?php
} else if (isset($data['email'])){
?>
                    <input disabled id="email" name="email" type="text" value="<?php print $data['email']?>" title="<?php print $LANGDATA['L_EMAIL_H']?>"/>                
<?php
}
?>
                </dd>
            </dl>
            <dl>
                <dt><label><?php print $LANGDATA['L_NEW_PASSWORD']?> :</label><br/>
                    <span><?php print $LANGDATA['L_NEW_PASSWORD_H']?> </span>
                </dt>
                <dd>
                    <input  readonly onfocus="this.removeAttribute('readonly');"  type="password" name="new_password" id="new_password" title="<?php print $LANGDATA['L_NEW_PASSWORD_H']?>" autocomplete="off"/>
                </dd>
            </dl>
            <dl>
                <dt><label><?php print $LANGDATA['L_RPASSWORD']?></label><br/>
                    <span><?php print $LANGDATA['L_R_PASSWORD_H']?> </span>
                </dt>
                <dd>
                        <input  type="password" name="r_password" id="r_password" title="<?php print $LANGDATA['L_R_PASSWORD_H']?>" autocomplete="off"/>
                </dd>
            </dl> 
            <dl>
                <dt><label><?php print $LANGDATA['L_PASSWORD']?></label><br/>
                    <span><?php print $LANGDATA['L_CUR_PASSWORD_H']?> </span>
                </dt>
                <dd>
                    <input required type="password" name="cur_password" id="cur_password" title="<?php print $LANGDATA['L_CUR_PASSWORD_H']?>" autocomplete="off"/>
                </dd>
            </dl>                             
            <p class="inputBtnSend"><input type="submit" id="profile" name="profile" value="<?php print $LANGDATA['L_SEND']?>" class=""  /></p>                                    
        </form>        
    </div>
</div>