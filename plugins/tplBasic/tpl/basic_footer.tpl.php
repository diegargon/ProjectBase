<?php
global $tpldata;
?>

<footer>
<?php 
    if (!empty($tpldata['FOOTER'])) {
        echo $tpldata['FOOTER'];
    } else {
        echo "<p>Copyright @2016 Diego García</p>";
    }
    ?>
</footer>
</body>
</html>

