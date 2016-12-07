<?php
!defined('IN_WEB') ? exit : true;
?>
<body id="Top">
    <div class="container">
        <div class="row1">
            <header id="header" class="clear">
                <?php isset($tpldata['ADD_HEADER_BEGIN']) ? print $tpldata['ADD_HEADER_BEGIN'] : false ?>  
                <?php if ($config['NAV_MENU']) { ?>
                    <script>
                        function toggleMenu() {
                            document.getElementsByClassName("main-nav")[0].classList.toggle("responsive");
                        }
                    </script>
                    <nav id="main-nav">
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
                <div id="brand">
                    <a href="/<?php $config['FRIENDLY_URL'] ? print $config['WEB_LANG'] : print "?lang={$config['WEB_LANG']}"; ?>">
                        <?php echo $config['WEB_NAME'] ?></a><br/>
                    <span><?php echo $config['WEB_DESC'] ?></span>
                </div>
                <?php
                if (!empty($tpldata['SECTIONS_MENU'])) {
                    ?>
                    <div id="sections_menu">
                        <ul>
                            <?php
                            !empty($tpldata['SECTIONS_MENU']) ? print $tpldata['SECTIONS_MENU'] : false;
                            ?>
                        </ul>
                    </div>
                    <?php
                }
                if (!empty($tpldata['SECTIONS_SUBMENU'])) {
                    ?>
                    <div id="sections_submenu">
                        <ul>
                            <?php !empty($tpldata['SECTIONS_SUBMENU']) ? print $tpldata['SECTIONS_SUBMENU'] : null; ?>
                        </ul>
                    </div>
                    <?php
                }
                ?>
                <?php isset($tpldata['ADD_HEADER_END']) ? print $tpldata['ADD_HEADER_END'] : false ?>
            </header>
        </div>
        <?php
        isset($tpldata['PRE_ACTION_ADD_TO_BODY']) ? print $tpldata['PRE_ACTION_ADD_TO_BODY'] : false;
        !isset($tpldata['ADD_TO_BODY']) ? print "<p>Hello World</p>" : print $tpldata['ADD_TO_BODY'];
        isset($tpldata['POST_ACTION_ADD_TO_BODY']) ? print $tpldata['POST_ACTION_ADD_TO_BODY'] : false;
