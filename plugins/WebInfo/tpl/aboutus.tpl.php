<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div class="page">
    <div class="aboutus-container">
        <h1><a class="nodecor" href="<?= $config['WEB_URL'] ?>"><?= $config['WEB_NAME'] ?></a></h1>
        <p>
            Somos una pagina web independiente de noticias, opinión y artículos entre otros de ámbito social.<br> 
            Todo lector puede publicar artículos o noticias y participar en discusiones libremente siempre que cumpla las normas básicas de esta web.
        </p>
        <div class="vcard">
            <address>        
                <p><?= $LANGDATA['L_WEBINF_BY'] . ":" . $config['WEB_NAME'] ?></p>
                <p><?= $LANGDATA['L_WEBINF_VISITUS'] ?><a href="<?= $config['WEB_URL'] ?>"><?= $config['WEB_URL'] ?></a><br></p>
                <p><?= $config['WEB_NAME'] ?></p>
                <p><?= $LANGDATA['L_WEBINF_COUNTRY'] ?></p>
                <p><?= !empty($data['CONTACT']) ? $data['CONTACT'] : null ?></p>
            </address>
            <time datetime="<?= $config['CONTACT_TIME_DATATIME'] ?>"><?= $config['CONTACT_TIME_HUMAN'] ?></time>
        </div>
    </div>
</div>