<?php
global $tpldata;
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
                <li class=""><a href="./">Inicio</a></li>
            </ul>
        </nav>