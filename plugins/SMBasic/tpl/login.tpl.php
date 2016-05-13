<?php
global $tpldata;
/* 
 *  Copyright @ 2016 Diego Garcia
 */
?>
    <div  class="clear bodysize page">   
        <div class="loginbox">
            <form  action="" autocomplete="off" method="post"> 
				<h1>Log in</h1> 
				<p> 
					<label for="email"> Email </label>
					<input id="email" name="email" required="required" type="text" placeholder="mi@mail.com"/>
				</p>
				<p> 
					<label for="password"> Contraseña </label>
                                        <input id="password" name="password" required="required" type="password" autocomplete="off" placeholder="eg. X8df!90EO" /> 
				</p>
<!--
                                <p class="rememberme"> 
					<input type="checkbox" name="loginkeeping" id="loginkeeping" value="loginkeeping" /> 
					<label for="loginkeeping">Recuerdame</label>
				</p>
-->
				<p class="login button"> 
                                    <input type="submit" id="login" name="login" class="btnLogin" value="Login" /> 
				</p>
				<p class="change_link">
					¿no eres miembro?
					<a href="register.php" class="to_register">Registrate</a>
				</p>
			</form>
            
            
        </div>
    </div>
    
