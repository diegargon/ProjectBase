<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">
    <?php if (!empty($data['oAuth_data'])) { ?>
        <div class="register_oauth_box">
            <?= $data['oAuth_data'] ?>
        </div>
    <?php } ?>
    <div class="register_box">
        <form  id="register_form" action="#" method="post">
            <h1><?= $LANGDATA['L_REGISTER'] ?></h1>
            <?php
            if ($config['smbasic_need_username']) {
                ?>
                <p>
                    <label for="username"><?= $LANGDATA['L_USERNAME'] ?> </label>
                    <input id="username" name="username"  type="text" placeholder=""/>
                </p>
                <?php
            }
            ?>
            <p>
                <label for="email"><?= $LANGDATA['L_EMAIL'] ?> </label>
                <input id="email" name="email" required="required" type="text" placeholder="<?= $LANGDATA['L_EMAIL_EXAMPLE'] ?>"/>
            </p>
            <p>
                <label for="password"><?= $LANGDATA['L_PASSWORD'] ?> </label>
                <input id="password" name="password" required="required" type="password" placeholder=""/>
            </p>
            <p>
                <label for="rpassword"><?= $LANGDATA['L_RPASSWORD'] ?> </label>
                <input id="rpassword" name="rpassword" required="required" type="password" placeholder=""/>
            </p>
            <p>
                <a href="<?= $data['terms_url'] ?>" target="_blank"><?= $LANGDATA['L_TOS'] ?></a><input id="tos" name="tos" required="required" type="checkbox"/>
            </p>
            <p class="register button">
                <input type="submit" id="register" name="register" class="btnRegister" value="<?= $LANGDATA['L_REGISTER'] ?>" />
            </p>
        </form>
    </div>
</div>