<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
 ?>
<div  class="clear bodysize page">   
    <div class="login_box">
        <form  id="login_form" action=""  method="post"> 
            <h1><?php print $LANGDATA['L_LOGIN'] ?></h1> 
            <p> 
                <label for="email"><?php print $LANGDATA['L_EMAIL'] ?></label>
                <input id="email" name="email" required="required" type="text" placeholder="<?php print $LANGDATA['L_EMAIL_EXAMPLE'] ?>"/>
            </p>
            <p> 
                <label id="label_password" for="password"><?php print $LANGDATA['L_PASSWORD'] ?></label>
                <input id="password" name="password" required="required" type="password"  placeholder="<?php print $LANGDATA['L_PASSWORD_EXAMPLE'] ?>" /> 
            </p>
            <p class="rememberme">                                 
                <?php if ($config['smbasic_session_persistence']) { ?>
                    <input  type="checkbox" name="rememberme" id="rememberme" value="2" /> 
                    <label id="label_rememberme" for="rememberme"><?php print $LANGDATA['L_REMEMBERME'] ?></label>
                <?php } ?>
                <input type="checkbox" name="reset_password_chk" id="reset_password_chk" value="3" /> 
                <label  for="reset_password"><?php print $LANGDATA['L_RESET_PASSWORD'] ?></label>
            </p>                                
            <p class="login button"> 
                <input type="submit" id="login" name="login" class="btnLogin" value="<?php print $LANGDATA['L_LOGIN'] ?>" /> 
            </p>
            <p class="login button"> 
                <input hidden type="submit" id="reset_password_btn" name="reset_password" class="btnReset" value="<?php print $LANGDATA['L_RESET_PASSWORD_BTN'] ?>" /> 
            </p>                                
            <p class="change_link">
                <?php print $LANGDATA['L_REGISTER_MSG'] ?>
                <a href="<?php print $data['register_url'] ?>" class="to_register"><?php print $LANGDATA['L_REGISTER'] ?></a>
            </p>
        </form>            
    </div>
</div>