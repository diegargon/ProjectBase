<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
?>
    <div  class="clear bodysize page">   
        <?php isset($tpldata['ADD_TOP_ADMIN']) ? print $tpldata['ADD_TOP_ADMIN']:false ?>		
        <div id="admin_container">
            <div class="tabs_container">
            <ul>
                <li class="admin_tabs"><a href="">General</a></li>
                <li class="admin_tabs"><a href="">Opcion 1</a></li>
                <li class="admin_tabs"><a href="">Opcion 2</a></li>
                <li class="admin_tabs"><a href="">Opcion 3</a></li>            	    
            </ul>
            </div>
	    <div id="content">
	    </div>
        </div>
        <?php isset($tpldata['ADD_BOTTOM_ADMIN']) ? print $tpldata['ADD_BOTTOM_ADMIN']:false ?>            
    </div>