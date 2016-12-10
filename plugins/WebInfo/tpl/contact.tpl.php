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
        <form method="post" action="#" id="post">
            <div class="info-panel">
                jhiohoihihoih oih oihoi h
            </div>
            <br class="clear" />
            <div class="content">
                <fieldset class="fields2">
                    <dl>
                        <dt><label>Destinatario:</label></dt>
                        <dd><strong>Administrador</strong></dd>
                    </dl>
                    <dl>
                        <dt><label for="email">Su dirección de correo electrónico:</label></dt>
                        <dd><input class="inputbox autowidth" type="text" name="email" id="email" size="50" maxlength="100" tabindex="1" value="" /></dd>
                    </dl>
                    <dl>
                        <dt><label for="name">Su nombre:</label></dt>
                        <dd><input class="inputbox autowidth" type="text" name="name" id="name" size="50" tabindex="2" value="" /></dd>
                    </dl>
                    <dl>
                        <dt><label for="subject">Asunto:</label></dt>
                        <dd><input class="inputbox autowidth" type="text" name="subject" id="subject" size="50" tabindex="3" value="" /></dd>
                    </dl>
                    <dl>
                        <dt><label for="message">Cuerpo del mensaje:</label><br />
                            <span>Este mensaje será enviado como texto plano, no incluya HTML o BBCode. La dirección del remitente será su dirección de email.</span></dt>
                        <dd><textarea class="inputbox" name="message" id="message" rows="15" cols="76" tabindex="4"></textarea></dd>
                    </dl>
                </fieldset>
            </div>
                <div class="content">
                    <fieldset class="submit-buttons">
                        <input type="submit" tabindex="6" name="submit" class="button1" value="Enviar email" />
                    </fieldset>
                </div>
                <input type="hidden" name="creation_time" value="1481403113" />
                <input type="hidden" name="form_token" value="2f564ed4abc520061b53a644aca9b38208eb88ec" />
        </form>
    </div>
</div>
