<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">   
    <?= !empty($tpldata['ADD_TOP_ADMIN']) ? $tpldata['ADD_TOP_ADMIN'] : false ?>		
    <div id="admin_container">
        <div id="admin_tabs">
            <ul>
                <?php
                if ($tpldata['ADMIN_TAB_ACTIVE'] == 1) {
                    ?>
                    <li class="tab_active"><a href="admin&admtab=1" ><?= $LANGDATA['L_GENERAL'] ?></a></li>
                <?php } else { ?>
                    <li class=""><a href="admin&admtab=1" ><?= $LANGDATA['L_GENERAL'] ?></a></li>
                <?php } ?>

                <?= !empty($tpldata['ADD_ADMIN_MENU']) ? $tpldata['ADD_ADMIN_MENU'] : false ?>		
            </ul>
        </div>
        <div id="admin_content">
            <?= !empty($tpldata['ADD_ADMIN_CONTENT']) ? $tpldata['ADD_ADMIN_CONTENT'] : false ?>
        </div>
    </div>
    <?= !empty($tpldata['ADD_BOTTOM_ADMIN']) ? $tpldata['ADD_BOTTOM_ADMIN'] : false ?>            
</div>