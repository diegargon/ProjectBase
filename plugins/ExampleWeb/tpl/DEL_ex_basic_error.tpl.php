<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<div  class="clear bodysize page">   
    <div class="standard_box">
        <h1><?php print $tpldata['E_TITLE']?></h1> 
        <p class="p_center_big"> 
            <?php print $tpldata['E_MSG']?>                
        </p>
<?php        
if (!empty($tpldata['E_BACKLINK_TITLE'])) {
?>
        <p class="p_center_medium">
            <a href="<?php print $config['BACKLINK'] ?>" />
            <?php print $tpldata['E_BACKLINK_TITLE'] ?>
            </a>
        </p>           
<?php
}
?>
    </div>
</div>