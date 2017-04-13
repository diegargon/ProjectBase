<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<aside>
    <ul>
        <?= $data['ADM_ASIDE_OPTION'] ?>
    </ul>    
</aside>
<div id="admin_opt_content">
    <section>    
        <h1><?= !empty($data['ADM_CONTENT_H1']) ? $data['ADM_CONTENT_H1'] : false ?></h1>
        <h2><?= !empty($data['ADM_CONTENT_H2']) ? $data['ADM_CONTENT_H2'] : false ?></h2>
        <?= !empty($data['ADM_CONTENT']) ? $data['ADM_CONTENT'] : false ?>
    </section>
</div>