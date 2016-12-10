<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
plugin_start("Newspage");
news_cat_menu();
?>

<div class="page">
    <div class="aboutus-container">
        <h1><a class="nodecor" href="<?php print $config['WEB_URL'] ?>"><?php print $config['WEB_NAME'] ?></a></h1>
        <p>
            Somos una pagina web independiente de noticias, opinión y artículos entre otros de ámbito social.<br> 
            Todo lector puede publicar artículos o noticias y participar en discusiones libremente siempre que cumpla las normas básicas de esta web.
        </p>
        <div class="vcard">
            <address>        
                <p><?php print $LANGDATA['L_WEBINF_BY'] ?> <a href="mailto:<?php $config['CONTACT_EMAIL'] ?> "><?php print $config['WEB_NAME'] ?></a></p>
                <p><?php print $LANGDATA['L_WEBINF_VISITUS'] ?><a href="<?php print $config['WEB_URL'] ?>"><?php print $config['WEB_URL'] ?></a><br></p>
                <p><?php print $config['WEB_NAME'] ?></p>
                <p><?php print $LANGDATA['L_WEBINF_COUNTRY'] ?></p>
                <p><?php print $LANGDATA['L_WEBINF_CONTACT'] ?>: <a href="mailto:<?php print $config['CONTACT_EMAIL'] ?>"><?php print $config['CONTACT_EMAIL'] ?></a>
            </address>
            <time datetime="<?php print $config['CONTACT_TIME_DATATIME'] ?>"><?php print $config['CONTACT_TIME_HUMAN'] ?></time>
        </div>
    </div>
</div>