<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */

?>
    <div  class="clear bodysize page">   
        <?php !empty($tpldata['ADD_TOP_ADMIN']) ? print $tpldata['ADD_TOP_ADMIN']:false ?>		
        <div id="admin_container">
            <div class="tabs_container">
            <ul>
<?php
if ($tpldata['ADMIN_TAB_ACTIVE'] == 1) {
    ?>
                <li class="tab_active"><a href="?admtab=1" ><?php print $LANGDATA['L_GENERAL'] ?></a></li>
<?php } else { ?>
                <li class=""><a href="?admtab=1" ><?php print $LANGDATA['L_GENERAL'] ?></a></li>
<?php } ?>

<?php                 
if ($tpldata['ADMIN_TAB_ACTIVE'] == 2) {
?>            
                <li class="tab_active"><a href="?admtab=2">Opcion 2</a></li>
<?php } else { ?>
                <li><a href="?admtab=2">Opcion 2</a></li>
<?php }  ?>

<?php
if ($tpldata['ADMIN_TAB_ACTIVE'] == 3) {
?>            
                <li class="tab_active"><a href="?admtab=3">Opcion 3</a></li>
<?php } else { ?>
                <li><a href="?admtab=3">Opcion 3</a></li>
<?php } ?>

                <?php !empty($tpldata['ADD_ADMIN_MENU']) ? print $tpldata['ADD_ADMIN_MENU']:false ?>		
            </ul>
            </div>
	    <div id="admin_content">
                <?php !empty($tpldata['ADD_ADMIN_CONTENT']) ? print $tpldata['ADD_ADMIN_CONTENT']:false ?>
	    </div>
        </div>
        <?php !empty($tpldata['ADD_BOTTOM_ADMIN']) ? print $tpldata['ADD_BOTTOM_ADMIN']:false ?>            
    </div>