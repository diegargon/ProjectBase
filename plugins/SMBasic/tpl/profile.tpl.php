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
            <fieldset>
                <dl>
                    <dt><label><?php print $LANGDATA['L_USERNAME']?> :</label><br/>
                        <span>blah blah</span></dt>                           
                    <dd>
<?php
if ($config['smbasic_can_change_username']) {
?> 
                    <input type="text" name="username" id="username" value="<?php print $data['username']?>" title="<?php print $LANGDATA['L_USERNAME']?>" autocomplete="off"/>
<?php                
} else {
     print $data['username'];
}
?>
                    </dd>
                </dl>
                
            <dl>
                <dt><label><?php print $LANGDATA['L_EMAIL']?> :</label><br/>
                    <span>blash blah</span>
                </dt>
                <dd>
<?php
if ($config['smbasic_can_change_email']) {
?> 
                <input type="text" name="email" id="email" value="<?php print $data['email']?>" title="<?php print $LANGDATA['L_EMAIL']?>" autocomplete="off"/>
<?php                
} else {
     print $data['email'];
}
?>                
                </dd>
            </dl>
            
                <dl>
                    <dt><label><?php print $LANGDATA['L_NEW_PASSWORD']?> :</label><br/>
                        <span>blah blah</span>
                    </dt>
                    <dd>
                        <input type="password" name="new_password" id="password" title="<?php print $LANGDATA['L_NEW_PASSWORD']?>" autocomplete="off"/>
                    </dd>
                </dl>
                <dl>
                    <dt><label><?php print $LANGDATA['L_RPASSWORD']?></label><br/>
                        <span>blah balh</span>
                    </dt>
                    <dd>
                        <input type="password" name="r_password" id="r_password" title="<?php print $LANGDATA['L_RPASSWORD']?>" autocomplete="off"/>
                    </dd>
                </dl>
                <dl>
                    <dt><label><?php print $LANGDATA['L_PASSWORD']?></label><br/>
                        <span>blah balh</span>
                    </dt>
                    <dd>
                        <input type="password" name="cur_password" id="cur_password" title="<?php print $LANGDATA['L_PASSWORD']?>" autocomplete="off"/>
                    </dd>
                </dl>                
            </fieldset>
        </form>        
    </div>
</div>
