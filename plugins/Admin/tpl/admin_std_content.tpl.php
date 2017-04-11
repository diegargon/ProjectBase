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
        <h1><?php !empty($data['ADM_CONTENT_H1']) ? print $data['ADM_CONTENT_H1'] : false ?></h1>
        <h2><?php !empty($data['ADM_CONTENT_H2']) ? print $data['ADM_CONTENT_H2'] : false ?></h2>
        <?php !empty($data['ADM_CONTENT']) ? print $data['ADM_CONTENT'] : false ?>
    </section>
</div>