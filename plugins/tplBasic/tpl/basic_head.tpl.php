<?php
global $config;
global $tpldata;

/* 
 *  Copyright @ 2016 Diego Garcia
 */
?>
<!DOCTYPE html>


<html dir="<?php print $config['web_dir']?>" lang="<?php print $config['web_lang']?>">
    <head>
        <title><?php echo $config['TITLE']?></title>
        <meta charset="<?php echo $config['CHARSET']?>">
        <meta name="viewport" content="<?php print $config['web_viewport']?>">
        <meta name="keywords" content="<?php print $config['web_keywords']?>">        
        <meta name="description" content="<?php echo $config['WEBDESC'] ?>">
        <?php
        
        isset($tpldata['HEAD']) ? print $tpldata['HEAD'] : false;
        isset($tpldata['META']) ? print $tpldata['META'] : false;
        isset($tpldata['LINK']) ? print $tpldata['LINK'] : false;
        isset($tpldata['SCRIPTS']) ? print $tpldata['SCRIPTS'] : false;
        ?>
    </head>

