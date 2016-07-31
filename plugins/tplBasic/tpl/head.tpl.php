<?php
if (!defined('IN_WEB')) { exit; }
/* 
 *  Copyright @ 2016 Diego Garcia
 */
?>
<!DOCTYPE html>
<html dir="<?php print $config['WEB_DIR']?>" lang="<?php print $config['WEB_LANG']?>">
    <head>
        <title><?php echo $config['PAGE_TITLE']?></title>
        <meta charset="<?php echo $config['CHARSET']?>">
        <meta name="viewport" content="<?php print $config['PAGE_VIEWPORT']?>">
        <meta name="keywords" content="<?php print $config['PAGE_KEYWORDS']?>">
        <meta name="referrer" content="always">
        <meta name="description" content="<?php echo $config['PAGE_DESC'] ?>">
        <meta name="author" content="<?php print $config['PAGE_AUTHOR']?>">
        <?php
        
        isset($tpldata['HEAD']) ? print $tpldata['HEAD'] : false;
        isset($tpldata['META']) ? print $tpldata['META'] : false;
        isset($tpldata['LINK']) ? print $tpldata['LINK'] : false;
        isset($tpldata['SCRIPTS_TOP']) ? print $tpldata['SCRIPTS_TOP'] : false;  
        ?>
        <script>
            function toggleMenu() {
            document.getElementsByClassName("main-nav")[0].classList.toggle("responsive");
        }
        </script>        
    </head>

