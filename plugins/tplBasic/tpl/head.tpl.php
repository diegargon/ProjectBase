<?php
if (!defined('IN_WEB')) { exit; }
/* 
 *  Copyright @ 2016 Diego Garcia
 */
?>
<!DOCTYPE html>
<html dir="<?php print $config['WEB_DIR']?>" lang="<?php print $config['WEB_LANG']?>">
    <head>
        <title><?php echo $config['TITLE']?></title>
        <meta charset="<?php echo $config['CHARSET']?>">
        <meta name="viewport" content="<?php print $config['WEB_VIEWPORT']?>">
        <meta name="keywords" content="<?php print $config['WEB_KEYWORDS']?>">        
        <meta name="description" content="<?php echo $config['WEB_DESC'] ?>">
        <?php
        
        isset($tpldata['HEAD']) ? print $tpldata['HEAD'] : false;
        isset($tpldata['META']) ? print $tpldata['META'] : false;
        isset($tpldata['LINK']) ? print $tpldata['LINK'] : false;
        isset($tpldata['SCRIPTS']) ? print $tpldata['SCRIPTS'] : false;
        ?>
    </head>

