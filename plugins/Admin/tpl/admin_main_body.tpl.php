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
                <li><a href="?admtab=1" >General</a></li>
                <li><a href="">Opcion 1</a></li>
                <li><a href="">Opcion 2</a></li>
                <li><a href="">Opcion 3</a></li>            	    
                <?php !empty($tpldata['ADD_ADMIN_MENU']) ? print $tpldata['ADD_ADMIN_MENU']:false ?>		
            </ul>
            </div>
	    <div id="admin_content">
	    </div>
        </div>
        <?php !empty($tpldata['ADD_BOTTOM_ADMIN']) ? print $tpldata['ADD_BOTTOM_ADMIN']:false ?>            
    </div>