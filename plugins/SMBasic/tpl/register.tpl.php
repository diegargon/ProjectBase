<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">
    <div class="register_box">
        <form  id="register_form" action="" autocomplete="off" method="post">
            <h1><?php print $LANGDATA['L_REGISTER'] ?></h1>
            <?php
            if ($config['smbasic_need_username']) {
                ?>
                <p>
                    <label for="username"><?php print $LANGDATA['L_USERNAME'] ?> </label>
                    <input id="username" name="username"  type="text" placeholder=""/>
                </p>
                <?php
            }
            ?>
            <p>
                <label for="email"><?php print $LANGDATA['L_EMAIL'] ?> </label>
                <input id="email" name="email" required="required" type="text" placeholder="<?php print $LANGDATA['L_EMAIL_EXAMPLE'] ?>"/>
            </p>
            <p>
                <label for="password"><?php print $LANGDATA['L_PASSWORD'] ?> </label>
                <input id="password" name="password" required="required" type="password" placeholder=""/>
            </p>
            <p>
                <label for="rpassword"><?php print $LANGDATA['L_RPASSWORD'] ?> </label>
                <input id="rpassword" name="rpassword" required="required" type="password" placeholder=""/>
            </p>
            <p>
                <a href="/terms.php" target="_blank"><?php print $LANGDATA['L_TOS'] ?></a><input id="tos" name="tos" required="required" type="checkbox"/>
            </p>
            <p class="register button">
                <input type="submit" id="register" name="register" class="btnRegister" value="<?php print $LANGDATA['L_REGISTER'] ?>" />
            </p>
        </form>
    </div>
</div>