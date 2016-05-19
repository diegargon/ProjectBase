<?php
global $tpldata;
/* 
 *  Copyright @ 2016 Diego Garcia
 */
?>

        <div  class="clear bodysize page">   
            <?php isset($tpldata['ADD_TOP_NEWS']) ? print $tpldata['ADD_TOP_NEWS']:false ?>
            
             <?php echo $tpldata['FEATURED'] ?> 
            <section id="col1">
               <?php   echo $tpldata['COL1_ARTICLES'] ?>
            </section>
            <section id="col2">
               <?php   echo $tpldata['COL2_ARTICLES'] ?>
            </section>
            <section id="col3">
               <?php   echo $tpldata['COL3_ARTICLES'] ?>
            </section>            
            <?php isset($tpldata['ADD_BOTTOM_NEWS']) ? print $tpldata['ADD_BOTTOM_NEWS']:false ?>            
        </div>
    


