<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
        <div  class="clear bodysize page">   
            <?php isset($tpldata['ADD_TOP_NEWS']) ? print $tpldata['ADD_TOP_NEWS']:false ?>
            
             <?php isset($tpldata['FEATURED']) ? print $tpldata['FEATURED'] : false ?> 
            <div id="col1">
               <?php   isset($tpldata['COL1_ARTICLES']) ? $tpldata['COL1_ARTICLES'] : false ?>
            </div>
            <div id="col2">
               <?php   isset($tpldata['COL2_ARTICLES']) ? $tpldata['COL2_ARTICLES'] :false ?>
            </div>
            <div id="col3">
               <?php   isset($tpldata['COL3_ARTICLES']) ? $tpldata['COL1_ARTICLES'] : false ?>
            </div>            
            <?php isset($tpldata['ADD_BOTTOM_NEWS']) ? print $tpldata['ADD_BOTTOM_NEWS']:false ?>            
        </div>