<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div class="page">
    <div class="advertise-container">
        <h1><a class="nodecor" href="<?= $config['WEB_URL'] ?>"><?= $config['WEB_NAME'] ?></a></h1>
        <h1>Disponemos de estos tipos de banners de precios variables:</h1>
        <ol>
            <li><h2>Banner superior central</h2>
                <span>El banner se muestra en todas las paginas. Su medidas de ancho pueden ser como máximo de 900x60</span>
            </li>
            <li><h2>Banner de pagina global</h2>
                <span>El banner se muestra en todas las paginas de noticias/artículos excepto en la principal</span>
            </li>
            <li><h2>Banner de pagina simple preferente</h2>
                <span>El banner se muestra en una noticia y/o articulo en particular en la posición más alta. Solo puede haber uno.</span>
            </li>
            <li><h2>Banner de pagina simple no preferente</h2>
                <span>El banner se muestra en  una noticia y/o articulo en particular por debajo del preferente</span>
            </li>
        </ol>
        <p>Para más información contacte con: <a href="mailto:<?= $config['ADS_CONTACT'] ?>"><?= $config['ADS_CONTACT'] ?></a>
    </div>
</div>