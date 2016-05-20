<?php
global $LANGDATA;
global $config;
/* 
 *  Copyright @ 2016 Diego Garcia
 */
?>

<div  class="clear bodysize page">   
    <div class="profile_box">        
        <form  action="?" autocomplete="off" method="post"> 
                <h1><?php print $LANGDATA['L_PROFILE']?></h1> 
                <dl>
                    <dt><label><?php print $LANGDATA['L_USERNAME']?></label><br/>
                        <span><?php print $LANGDATA['L_USERNAME_H']?> </span></dt>                           
                    <dd>
<?php
if ($config['smbasic_can_change_username'] && isset($data['username'])) {
?> 
                    <input type="text" value="<?php print $data['username']?>" title="<?php print $LANGDATA['L_USERNAME_H']?>" autocomplete="off"/>
<?php                
} else if (isset($data['username'])) {
    ?>
                    <input disabled type="text" value="<?php print $data['username']?>" title="<?php print $LANGDATA['L_USERNAME_H']?>"/>
<?php                    
}
?>
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
                <input type="text" name="email" id="email" value="<?php print $data['email']?>" title="<?php print $LANGDATA['L_EMAIL_H']?>" autocomplete="off"/>
<?php                
} else if (isset($data['email'])){
?>
                <input disabled type="text" value="<?php print $data['email']?>" title="<?php print $LANGDATA['L_EMAIL_H']?>"/>                
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
                        <input type="password" name="new_password" id="password" title="<?php print $LANGDATA['L_NEW_PASSWORD_H']?>" autocomplete="off"/>
                    </dd>
                </dl>
                <dl>
                    <dt><label><?php print $LANGDATA['L_RPASSWORD']?></label><br/>
                        <span><?php print $LANGDATA['L_R_PASSWORD_H']?> </span>
                    </dt>
                    <dd>
                        <input type="password" name="r_password" id="r_password" title="<?php print $LANGDATA['L_R_PASSWORD_H']?>" autocomplete="off"/>
                    </dd>
                </dl>
                <dl>
                    <dt><label><?php print $LANGDATA['L_PASSWORD']?></label><br/>
                        <span><?php print $LANGDATA['L_CUR_PASSWORD_H']?> </span>
                    </dt>
                    <dd>
                        <input type="password" name="cur_password" id="cur_password" title="<?php print $LANGDATA['L_CUR_PASSWORD_H']?>" autocomplete="off"/>
                    </dd>
                </dl>  
                <p><input name="submit" value="Enviar" class="" type="submit"></p>                                    
        </form>        
    </div>
</div>
