<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<!DOCTYPE html>
<html dir="<?= $cfg['WEB_DIR'] ?>" lang="<?= $cfg['WEB_LANG'] ?>">
    <head>
        <meta charset="<?= $cfg['CHARSET'] ?>" />
        <meta name="viewport" content="<?= $cfg['PAGE_VIEWPORT'] ?>" />
        <title><?= $cfg['PAGE_TITLE'] ?></title>
        <meta name='language' content='<?= $cfg['WEB_LANG'] ?>' />
        <meta name='robots' content='all' />
        <meta name="keywords" content="<?= $cfg['PAGE_KEYWORDS'] ?>" />
        <meta name="news_keywords" content="<?= $cfg['PAGE_KEYWORDS'] ?>" />
        <meta name="referrer" content="origin-when-crossorigin" />
        <meta name="description" content="<?= $cfg['PAGE_DESC'] ?>" />
        <meta name="author" content="<?= $cfg['PAGE_AUTHOR'] ?> " />
        <meta name="distribution" content="global"  />
        <meta name="resource-type" content="document"  />
        <meta name="organization" content="<?= $cfg['WEB_NAME'] ?> "/> 
        <meta name="theme-color" content="#ffffff" />
        <?php
        isset($tpldata['HEAD']) ? print $tpldata['HEAD'] : false;
        isset($tpldata['META']) ? print $tpldata['META'] : false;
        ?>
        <link rel="apple-touch-icon icon" sizes="76x76" href="<?= $cfg['STATIC_SRV_URL'] ?>apple-touch-icon.png" />
        <link rel="icon" type="image/png" href="<?= $cfg['STATIC_SRV_URL'] ?>favicon-32x32.png" sizes="32x32" />
        <link rel="icon" type="image/png" href="<?= $cfg['STATIC_SRV_URL'] ?>favicon-16x16.png" sizes="16x16" />
        <link rel="manifest" href="<?= $cfg['STATIC_SRV_URL'] ?>manifest.json" />
        <link rel="mask-icon" href="<?= $cfg['STATIC_SRV_URL'] ?>safari-pinned-tab.svg" />        
        <link rel="icon" href="<?= $cfg['STATIC_SRV_URL'] ?>favicon.ico" type='image/x-icon' />
        <?php
        isset($tpldata['LINK']) ? print $tpldata['LINK'] : false;
        isset($tpldata['SCRIPTS_TOP']) ? print $tpldata['SCRIPTS_TOP'] : false;
        ?>
    </head>
