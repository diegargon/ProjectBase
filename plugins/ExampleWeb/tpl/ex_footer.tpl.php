<?php
if (!defined('IN_WEB')) { exit; }

global $config, $tpldata;

?>
    <div class="row3">
        <footer id="footer" class="clear">
<?php
if (isset($tpldata['FOOTER'])) {
    echo $tpldata['FOOTER'];
}  
?>
            <p class="fl_left">Copyright &copy; 2016 - 2016 Diego García All Rights Reserved</p>
            <p class="fl_right"><a href=""><?php  echo $config['TITLE']?></a></p>
        </footer>
    </div>

</div> <!-- Main Container ex_header.tpl -->
</div> <!-- Container -->
</body>
</html>