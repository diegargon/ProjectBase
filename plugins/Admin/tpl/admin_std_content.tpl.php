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
<h1><?php print $tpldata['ADM_CONTENT_DESC']?></h1>
<?php print $tpldata['ADM_CONTENT']?>
</section>