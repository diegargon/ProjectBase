<?php
global $config;
global $tpldata;
/* 
 *  Copyright @ 2016 Diego Garcia
 */
?>


<div class="main_container">
<div class="container">    
<div class="row1">        
        <header id="header" class="clear">

        <?php isset($tpldata['ADD_HEADER_BEGIN']) ? print $tpldata['ADD_HEADER_BEGIN'] : false ?>
            
        <div id="logo">
            <h1><a href="<?php echo $config['WEBURL']?>"><?php echo $config['TITLE']?></a></h1>
            <h2><?php echo $config['WEBDESC']?></h2>
        </div>

        <?php isset($tpldata['NAV']) ? print $tpldata['NAV'] :false ?>
          

        <?php isset($tpldata['ADD_HEADER_END']) ? print $tpldata['ADD_HEADER_END']:false ?>
        </header>
</div>