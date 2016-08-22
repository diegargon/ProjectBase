<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
        <div  class="clear bodysize page">   
            <?php isset($tpldata['ADD_TOP_NEWS']) ? print $tpldata['ADD_TOP_NEWS']:false ?>                         
            <section class="col1">
               <?php   isset($tpldata['COL1_ARTICLES']) ? print $tpldata['COL1_ARTICLES'] : false ?>
            </section>
            <section class="col2">
               <?php isset($tpldata['FEATURED']) ? print $tpldata['FEATURED'] : false ?> 
               <?php   isset($tpldata['COL2_ARTICLES']) ? print $tpldata['COL2_ARTICLES'] :false ?>
            </section>
            <section class="col3">
               <?php   isset($tpldata['COL3_ARTICLES']) ? print $tpldata['COL3_ARTICLES'] : false ?>
            </section>            
            <?php isset($tpldata['ADD_BOTTOM_NEWS']) ? print $tpldata['ADD_BOTTOM_NEWS']:false ?>            
        </div>