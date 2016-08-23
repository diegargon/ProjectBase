<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<div  class="clear bodysize page">   
    <div class="standard_box">
        <h1><?php print $data['BOX_TITLE'] ?></h1> 
        <p class="p_center_big"> 
            <?php print $data['BOX_MSG'] ?>                
        </p>
        <p class="p_center_medium">
            <a href="<?php print $data['BOX_BACKLINK'] ?>">
                <?php print $data['BOX_BACKLINK_TITLE'] ?>
            </a>
        </p>           
    </div>
</div>