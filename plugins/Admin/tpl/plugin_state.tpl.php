<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<p>
    <span><?= $LANGDATA['L_PL_NAME'] . $LANGDATA['L_SEP']?></span>
    <?= $data['plugin_name'] ?>
</p>
<p>
    <span><?= $LANGDATA['L_PL_ENABLE'] . $LANGDATA['L_SEP'] ?></span>
    <?php $data['enabled'] ? print $LANGDATA['L_PL_YES'] : print $LANGDATA['L_PL_NO'] ?>    
</p>
<p>
    <span><?= $LANGDATA['L_PL_PROVIDE'] . $LANGDATA['L_SEP'] ?> </span>
    <?= $data['provided'] ?>
</p>
<p>
    <span><?= $LANGDATA['L_PL_PRIORITY'] . $LANGDATA['L_SEP'] ?> </span>
    <?= $data['priority'] ?>
</p>
<p> 
    <span><?= $LANGDATA['L_PL_DEPEND'] . $LANGDATA['L_SEP'] ?> </span>
    <?= $data['depends'] ?>
</p>
<p>
    <span><?= $LANGDATA['L_PL_AUTOSTART'] . $LANGDATA['L_SEP'] ?></span>
    <?php $data['autostart'] ? print $LANGDATA['L_PL_YES'] : print $LANGDATA['L_PL_NO'] ?>    
</p>
<p> 
    <span><?= $LANGDATA['L_PL_OPTIONAL'] . $LANGDATA['L_SEP'] ?> </span>
    <?= $data['optional'] ?>
</p>