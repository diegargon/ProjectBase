<?php
if (!defined('IN_WEB')) { exit; }
?>
        <nav>
            <ul>
                <li class="nav_left"><a href="/<?php echo $config['WEB_LANG']?>"><?php print $LANGDATA['L_HOME']?></a></li>                
                <?php
                    isset($tpldata['NAV_ELEMENT']) ? print $tpldata['NAV_ELEMENT'] : false;
                ?>                
            </ul>
        </nav>