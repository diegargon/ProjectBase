<?php
if (!defined('IN_WEB')) { exit; }
?>
    <div class="row3">
        <footer id="footer" class="clear">
<?php
    isset($tpldata['ADD_TO_FOOTER']) ? print $tpldata['ADD_TO_FOOTER'] : false;
?>
            <p class="fl_left">Copyright &copy; 2016 - 2016 Diego García All Rights Reserved</p>
            <p class="fl_right"><a href=""><?php echo $config['TITLE']?></a></p>
        </footer>
    </div>

</div> <!-- Main Container ex_header.tpl -->
</div> <!-- Container -->
</body>
</html>