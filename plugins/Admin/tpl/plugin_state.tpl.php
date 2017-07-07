<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<p>
    <span><?= $LNG['L_PL_NAME'] . $LNG['L_SEP'] ?></span>
    <?= $data['plugin_name'] ?>
</p>
<p>
    <span><?= $LNG['L_PL_ENABLE'] . $LNG['L_SEP'] ?></span>
    <?= $data['enabled'] ? $LNG['L_PL_YES'] : $LNG['L_PL_NO'] ?>    
</p>
<p>
    <span><?= $LNG['L_PL_PROVIDE'] . $LNG['L_SEP'] ?> </span>
    <?= $data['provided'] ?>
</p>
<p>
    <span><?= $LNG['L_PL_PRIORITY'] . $LNG['L_SEP'] ?> </span>
    <?= $data['priority'] ?>
</p>
<p> 
    <span><?= $LNG['L_PL_DEPEND'] . $LNG['L_SEP'] ?> </span>
    <?= $data['depends'] ?>
</p>
<p>
    <span><?= $LNG['L_PL_AUTOSTART'] . $LNG['L_SEP'] ?></span>
    <?= $data['autostart'] ? $LNG['L_PL_YES'] : $LNG['L_PL_NO'] ?>    
</p>
<p> 
    <span><?= $LNG['L_PL_OPTIONAL'] . $LNG['L_SEP'] ?> </span>
    <?= $data['optional'] ?>
</p>