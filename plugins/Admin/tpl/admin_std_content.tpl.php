<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<aside>
    <ul>
        <?php print $tpldata['ADM_ASIDE_OPTION']?>
    </ul>    
</aside>
<section>    
<h1><?php !empty($tpldata['ADM_CONTENT_DESC']) ? print $tpldata['ADM_CONTENT_DESC'] : false ?></h1>
<?php !empty($tpldata['ADM_CONTENT'])? print $tpldata['ADM_CONTENT'] : false ?>
</section>