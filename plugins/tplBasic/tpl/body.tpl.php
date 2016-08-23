<?php
if (!defined('IN_WEB')) { exit; }
?>
<body id="Top">
    <div class="main_container">
        <div class="container">    
            <div class="row1">        
                <header id="header" class="clear">
                    <?php isset($tpldata['ADD_HEADER_BEGIN']) ? print $tpldata['ADD_HEADER_BEGIN'] : false ?>            
                    <div id="brand">
                        <a href="/<?php $config['FRIENDLY_URL'] ? print $config['WEB_LANG'] : print "?lang={$config['WEB_LANG']}"; ?>">
                            <?php echo $config['TITLE'] ?></a><br/>
                        <span><?php echo $config['WEB_DESC'] ?></span>
                    </div>
                    <?php if ($config['NAV_MENU']) { ?>
                        <nav>
                            <ul class="main-nav">              
                                <li class="nav_right resp-icon"><a rel="nofollow" href="javascript:void(0);" onclick="toggleMenu()">&#9776;</a></li>                
                                <?php if ($config['NAV_MENU_HOME']) { ?>
                                    <li class='nav_left lihome'>
                                        <a href='/<?php
                                        $config['FRIENDLY_URL'] ? print $config['WEB_LANG'] : print "?lang={$config['WEB_LANG']}";
                                        ?>'><img src='<?php print $config['IMG_HOME'] ?>' alt='<?php print $LANGDATA['L_HOME'] ?>' />                        
                                        </a></li>           
                                <?php } ?>
                                <?php isset($tpldata['NAV_ELEMENT']) ? print $tpldata['NAV_ELEMENT'] : false; ?>

                            </ul>
                        </nav>            
                    <?php } ?>
                    <?php isset($tpldata['ADD_HEADER_END']) ? print $tpldata['ADD_HEADER_END'] : false ?>
                </header>
            </div>
            <?php
            isset($tpldata['PRE_ACTION_ADD_TO_BODY']) ? print $tpldata['PRE_ACTION_ADD_TO_BODY'] : false;
            !isset($tpldata['ADD_TO_BODY']) ? print "<p>Hello World</p>" : print $tpldata['ADD_TO_BODY'];
            isset($tpldata['POST_ACTION_ADD_TO_BODY']) ? print $tpldata['POST_ACTION_ADD_TO_BODY'] : false;
