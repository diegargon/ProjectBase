<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
plugin_start("Newspage");
news_cat_menu();
?>
<div class="page">
    <div class="contact-container">
        <div id="info-panel" class="info-panel">
            <span><?php print $LANGDATA['L_WEBINFO_CONTACT_INFO'] ?></span>
        </div>
        <br class="clear" />        
        <form method="post" action="#" id="contact_form">
            <div class="contact_form">
                <label for="email"><?php print $LANGDATA['L_CONTACT_YOUREMAIL'] ?></label>
                <input class="form_inputbox " type="text" name="email" id="email" size="50" maxlength="100" tabindex="1" value="" />
                <label for="name"><?php print $LANGDATA['L_CONTACT_YOURNAME'] ?></label>
                <input class="form_inputbox " type="text" name="name" id="name" size="50" tabindex="2" value="" />
                <label for="subject"><?php print $LANGDATA['L_CONTACT_SUBJECT'] ?></label>
                <input class="form_inputbox " type="text" name="subject" id="subject" size="50" tabindex="3" value="" />
                <label for="message"><?php print $LANGDATA['L_CONTACT_BODY'] ?></label>
                <textarea class="form_inputbox " name="message" id="message" rows="15" cols="76" tabindex="4"></textarea>
            </div>
            <div class="contact_form">
                <?php if (defined('RECAPTCHA') && $config['WEBINFO_RECAPTCHA']) { ?>
                    <div id="captcha" class="center">
                        <div class="g-recaptcha" data-callback="rc_cb" data-sitekey="<?php print $config['RC_PUBLIC_KEY'] ?>">
                        </div>
                    </div>
                <?php } ?>              
                <div id="submitbtn" class="center">
                    <input disabled id="btnSend" type="submit" tabindex="6" name="submit" value="<?php print $LANGDATA['L_SEND'] ?>" />
                </div>
            </div>        
        </form>
    </div>
</div>
