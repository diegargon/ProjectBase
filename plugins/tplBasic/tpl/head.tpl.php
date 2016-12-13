<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<!DOCTYPE html>
<html dir="<?php print $config['WEB_DIR'] ?>" lang="<?php print $config['WEB_LANG'] ?>">
    <head>
        <meta charset="<?php echo $config['CHARSET'] ?>" />
        <meta name="viewport" content="<?php print $config['PAGE_VIEWPORT'] ?>" />
        <title><?php echo $config['PAGE_TITLE'] ?></title>
        <meta name='language' content='<?php print $config['WEB_LANG'] ?>' />
        <meta name='robots' content='all' />
        <meta name="keywords" content="<?php print $config['PAGE_KEYWORDS'] ?>" />
        <meta name="news_keywords" content="<?php print $config['PAGE_KEYWORDS'] ?>" />
        <meta name="referrer" content="origin-when-crossorigin" />
        <meta name="description" content="<?php echo $config['PAGE_DESC'] ?>" />
        <meta name="author" content="<?php print $config['PAGE_AUTHOR'] ?> " />
        <meta name="distribution" content="global"  />
        <meta name="resource-type" content="document"  />
        <meta name="organization" content="<?php print $config['WEB_NAME'] ?> "/> 
        <meta name="theme-color" content="#ffffff" />

        <?php
        isset($tpldata['HEAD']) ? print $tpldata['HEAD'] : false;
        isset($tpldata['META']) ? print $tpldata['META'] : false;
        ?>
        <link rel="apple-touch-icon" sizes="76x76" href="<?php print $config['STATIC_SRV_URL'] ?>apple-touch-icon.png" />
        <link rel="icon" type="image/png" href="<?php print $config['STATIC_SRV_URL'] ?>favicon-32x32.png" sizes="32x32" />
        <link rel="icon" type="image/png" href="<?php print $config['STATIC_SRV_URL'] ?>favicon-16x16.png" sizes="16x16" />
        <link rel="manifest" href="<?php print $config['STATIC_SRV_URL'] ?>manifest.json" />
        <link rel="mask-icon" href="<?php print $config['STATIC_SRV_URL'] ?>safari-pinned-tab.svg" />        
        <link rel="icon" href="<?php print $config['STATIC_SRV_URL'] ?>favicon.ico" type='image/x-icon' />
        <?php
        isset($tpldata['LINK']) ? print $tpldata['LINK'] : false;
        isset($tpldata['SCRIPTS_TOP']) ? print $tpldata['SCRIPTS_TOP'] : false;
        ?>
    </head>
