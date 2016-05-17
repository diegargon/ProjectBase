<?php
global $config;
global $tpldata;
global $LANGDATA;

/* 
 *  Copyright @ 2016 Diego Garcia
 */
?>
    <div  class="clear bodysize page">   
        <div class="loginbox">
            <form  action="" autocomplete="off" method="post"> 
				<h1><?php print $LANGDATA['L_LOGIN']?></h1> 
				<p> 
					<label for="email"><?php print $LANGDATA['L_EMAIL']?></label>
					<input id="email" name="email" required="required" type="text" placeholder="<?php print $LANGDATA['L_EMAIL_EXAMPLE']?>"/>
				</p>
				<p> 
					<label for="password"><?php print $LANGDATA['L_PASSWORD']?></label>
                                        <input id="password" name="password" required="required" type="password" autocomplete="off" placeholder="<?php print $LANGDATA['L_PASSWORD_EXAMPLE']?>" /> 
				</p>

                                <p class="rememberme"> 
					<input type="checkbox" name="rememberme" id="rememberme" value="2" /> 
					<label for="rememberme"><?php print $LANGDATA['L_REMEMBERME']?></label>
				</p>
                                <?php if($config['smbasic_session_persistence']) {?>                                    
				<p class="login button"> 
                                    <input type="submit" id="login" name="login" class="btnLogin" value="<?php print $LANGDATA['L_LOGIN']?>" /> 
				</p>
                                <?php } ?>
				<p class="change_link">
					<?php print $LANGDATA['L_REGISTER_MSG']?>
					<a href="register.php" class="to_register"><?php print $LANGDATA['L_REGISTER']?></a>
				</p>
			</form>
            
            
        </div>
    </div>
    
