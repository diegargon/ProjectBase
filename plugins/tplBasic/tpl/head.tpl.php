<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<!DOCTYPE html>
<html dir="<?php print $config['WEB_DIR'] ?>" lang="<?php print $config['WEB_LANG'] ?>">
    <head>
        <title><?php echo $config['PAGE_TITLE'] ?></title>
        <meta charset="<?php echo $config['CHARSET'] ?>" />
        <meta name='language' content='<?php print $config['WEB_LANG'] ?>' />
        <meta name="viewport" content="<?php print $config['PAGE_VIEWPORT'] ?>" />
        <meta name='robots' content='all' />
        <meta name="keywords" content="<?php print $config['PAGE_KEYWORDS'] ?>" />
        <meta name="news_keywords" content="<?php print $config['PAGE_KEYWORDS'] ?>" />
        <meta name="referrer" content="always" />
        <meta name="description" content="<?php echo $config['PAGE_DESC'] ?>" />
        <meta name="author" content="<?php print $config['PAGE_AUTHOR'] ?> " />
        <meta name="distribution" content="global"  />
        <meta name="resource-type" content="document"  />
        <meta name="organization" content="<?php print $config['WEB_NAME'] ?> "/>        
        <?php
        isset($tpldata['HEAD']) ? print $tpldata['HEAD'] : false;
        isset($tpldata['META']) ? print $tpldata['META'] : false;
        ?>
        <link rel="shortcut icon" href="<?php print $config['WEB_URL'] ?>favicon.ico" type='image/x-icon' />
        <?php
        isset($tpldata['LINK']) ? print $tpldata['LINK'] : false;
        isset($tpldata['SCRIPTS_TOP']) ? print $tpldata['SCRIPTS_TOP'] : false;
        ?>
    </head>
