<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<p>
    <span><?php print $LANGDATA['L_PL_NAME'] . $LANGDATA['L_SEP']?></span>
    <?php print $data['plugin_name'] ?>
</p>
<p>
    <span><?php print $LANGDATA['L_PL_ENABLE'] . $LANGDATA['L_SEP'] ?></span>
    <?php $data['enabled'] ? print $LANGDATA['L_PL_YES'] : print $LANGDATA['L_PL_NO'] ?>    
</p>
<p>
    <span><?php print $LANGDATA['L_PL_PROVIDE'] . $LANGDATA['L_SEP'] ?> </span>
    <?php print $data['provided'] ?>
</p>
<p>
    <span><?php print $LANGDATA['L_PL_PRIORITY'] . $LANGDATA['L_SEP'] ?> </span>
    <?php print $data['priority'] ?>
</p>
<p> 
    <span><?php print $LANGDATA['L_PL_DEPEND'] . $LANGDATA['L_SEP'] ?> </span>
    <?php print $data['depends'] ?>
</p>
<p>
    <span><?php print $LANGDATA['L_PL_AUTOSTART'] . $LANGDATA['L_SEP'] ?></span>
    <?php $data['autostart'] ? print $LANGDATA['L_PL_YES'] : print $LANGDATA['L_PL_NO'] ?>    
</p>
<p> 
    <span><?php print $LANGDATA['L_PL_OPTIONAL'] . $LANGDATA['L_SEP'] ?> </span>
    <?php print $data['optional'] ?>
</p>