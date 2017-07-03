<?php
!defined('IN_WEB') ? exit : true;
?>
<body id="Top">
    <div class="container">
        <div class="row1">
            <header id="header" class="clear">
                <?= isset($tpldata['ADD_HEADER_BEGIN']) ? $tpldata['ADD_HEADER_BEGIN'] : null ?>  
                <?php if ($config['NAV_MENU']) { ?>
                    <script>
                        function toggleMenu() {
                            document.getElementsByClassName("header-menu")[0].classList.toggle("responsive");
                        }
                    </script>
                    <div id="header-menu">
                        <ul class="header-menu">
                            <li class="nav_right resp-icon"><a rel="nofollow" href="javascript:void(0);" onclick="toggleMenu()">&#9776;</a></li>
                            <?php if ($config['HEADER_MENU_HOME']) { ?>
                                <li class='nav_left lihome zero'>
                                    <a href='/<?= $config['FRIENDLY_URL'] ? $config['WEB_LANG'] : "?lang={$config['WEB_LANG']}";?>/'>
                                        <img width=20 height=20 src='<?= $config['IMG_HOME'] ?>' alt='<?= $LANGDATA['L_HOME'] ?>' />
                                    </a></li>
                            <?php } ?>
                            <?= isset($tpldata['HEADER_MENU_ELEMENT']) ? $tpldata['HEADER_MENU_ELEMENT'] : null ?>
                        </ul>
                    </div>
                <?php } ?>
                <div id="brand">
                    <a href="/<?= $config['FRIENDLY_URL'] ? $config['WEB_LANG'] : "?lang={$config['WEB_LANG']}"; ?>/">
                        <?= $config['WEB_NAME'] ?></a><br/>
                    <span><?= $config['WEB_DESC'] ?></span>
                </div>
                <?php if (!empty($tpldata['SECTIONS_NAV'])) { ?>
                    <nav id="sections_nav">
                        <ul>
                            <?= !empty($tpldata['SECTIONS_NAV']) ? print $tpldata['SECTIONS_NAV'] : null ?>
                        </ul>
                    </nav>
                <?php }
                if (!empty($tpldata['SECTIONS_SUBMENU'])) {
                ?>
                    <nav id="sections_submenu">
                        <ul>
                            <?= !empty($tpldata['SECTIONS_SUBMENU']) ? $tpldata['SECTIONS_SUBMENU'] : null ?>
                        </ul>
                    </nav>
                <?php } ?>
                <?= isset($tpldata['ADD_HEADER_END']) ? $tpldata['ADD_HEADER_END'] : null ?>
            </header>
        </div>
        <?php
        isset($tpldata['PRE_ACTION_ADD_TO_BODY']) ? print $tpldata['PRE_ACTION_ADD_TO_BODY'] : null;
        !isset($tpldata['ADD_TO_BODY']) ? print "<p>Hello World</p>" : print $tpldata['ADD_TO_BODY'];
        isset($tpldata['POST_ACTION_ADD_TO_BODY']) ? print $tpldata['POST_ACTION_ADD_TO_BODY'] : null;