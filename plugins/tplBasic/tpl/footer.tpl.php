<?php
!defined('IN_WEB') ? exit : true;
?>
<div class="row3">
    <footer id="footer" class="clear">
        <?= isset($tpldata['ADD_TO_FOOTER']) ? $tpldata['ADD_TO_FOOTER'] : null ?>
        <small class="fl_left"><?= $cfg['FOOT_COPYRIGHT'] ?></small>
        <small class="fl_right"><a href="<?= $cfg['WEB_URL'] ?>"><?= $cfg['TITLE'] ?></a></small>
    </footer>
</div>
</div> <!-- Container -->
<?= isset($tpldata['SCRIPTS_BOTTOM']) ? $tpldata['SCRIPTS_BOTTOM'] : null ?>
<br/><br/>
</body>
</html>