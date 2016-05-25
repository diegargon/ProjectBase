<?php
if (!defined('IN_WEB')) { exit; }
?>
<footer>
<?php 
    if (!empty($tpldata['ADD_TO_FOOTER'])) {
        echo $tpldata['ADD_TO_FOOTER'];
    } else {
        echo "<p>Copyright @2016 Diego García</p>";
    }
    ?>
</footer>
</body>
</html>

