<?php
!defined('IN_WEB') ? exit : true;
?>
<div class="row3">
    <footer id="footer" class="clear">
        <?php isset($tpldata['ADD_TO_FOOTER']) ? print $tpldata['ADD_TO_FOOTER'] : false; ?>
        <p class="fl_left"><?php print $config['FOOT_COPYRIGHT'] ?></p>
        <p class="fl_right"><a href=""><?php print $config['TITLE'] ?></a></p>
    </footer>
</div>
</div> <!-- Container -->
<?php isset($tpldata['SCRIPTS_BOTTOM']) ? print $tpldata['SCRIPTS_BOTTOM'] : false; ?>
</body>
</html>