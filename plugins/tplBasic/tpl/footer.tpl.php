<?php
!defined('IN_WEB') ? exit : true;
?>
<div class="row3">
    <footer id="footer" class="clear">
        <?= isset($tpldata['ADD_TO_FOOTER']) ? $tpldata['ADD_TO_FOOTER'] : null ?>
        <small class="fl_left"><?= $config['FOOT_COPYRIGHT'] ?></small>
        <small class="fl_right"><a href="<?= $config['WEB_URL'] ?>"><?= $config['TITLE'] ?></a></small>
    </footer>
</div>
</div> <!-- Container -->
<?= isset($tpldata['SCRIPTS_BOTTOM']) ? $tpldata['SCRIPTS_BOTTOM'] : null ?>
</body>
</html>