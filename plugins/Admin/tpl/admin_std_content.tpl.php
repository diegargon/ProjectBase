<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<aside>
    <ul>
        <?php print $tpldata['ADM_ASIDE_OPTION']?>
    </ul>    
</aside>
<div id="admin_opt_content">
<section>    
<h1><?php !empty($tpldata['ADM_CONTENT_H1']) ? print $tpldata['ADM_CONTENT_H1'] : false ?></h1>
<h2><?php !empty($tpldata['ADM_CONTENT_H2']) ? print $tpldata['ADM_CONTENT_H2'] : false ?></h2>
<?php !empty($tpldata['ADM_CONTENT'])? print $tpldata['ADM_CONTENT'] : false ?>
</section>
</div>