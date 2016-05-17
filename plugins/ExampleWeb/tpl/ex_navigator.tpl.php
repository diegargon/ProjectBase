<?php
global $config;
global $tpldata;
global $LANGDATA;

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
                
                <!--
                <li><a href="">Text Link</a></li>
                <li><a href="">Text Link</a></li>
                <li><a href="">Text Link</a></li>
                <li><a href="">Text Link</a></li>
                -->
                <li class="left"><a href="/<?php echo $config['WEB_LANG']?>"><?php print $LANGDATA['L_HOME']?></a></li>
            </ul>
        </nav>