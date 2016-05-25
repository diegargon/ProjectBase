<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<div>
    <p>
         <?php isset($tpldata['ERROR_TITLE']) ? print $tpldata['ERROR_TITLE'] : false ?>
    </p>
    <p>
        <?php isset($tpldata['ERROR_MSG']) ? print $tpldata['ERROR_MSG'] : false ?>
    </p>
<?php        
if (!empty($tpldata['ERROR_BACKLINK']) && !empty($tpldata['ERROR_BACKLINK_TITLE'])) {
?>
    <p class="p_center_medium">
        <a href="<?php !empty($tpldata['ERROR_BACKLINK']) ? print $tpldata['ERROR_BACKLINK'] : false ?>" />
        <?php print $tpldata['ERROR_BACKLINK_TITLE'] ?>
    </a>
    </p>           
<?php
}
?>    
</div>