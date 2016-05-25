<?php
if (!defined('IN_WEB')) { exit; }

global $config, $tpldata, $LANGDATA;

?>

        <nav>
            <ul>
                <?php
                
                    if(isset($tpldata['NAV_ELEMENT'])) {
                        echo $tpldata['NAV_ELEMENT'];
                    } else {
                        print_debug("tpldata/nav not set<br/>");
                    }
                ?>

                <li class="left"><a href="/<?php echo $config['WEB_LANG']?>"><?php print $LANGDATA['L_HOME']?></a></li>
            </ul>
        </nav>