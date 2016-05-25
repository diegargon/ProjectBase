<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<div  class="clear bodysize page">   
    <div class="standard_box">
        <h1><?php print $tpldata['ERROR_TITLE']?></h1> 
        <p class="p_center_big"> 
            <?php print $tpldata['ERROR_MSG']?>                
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
</div>