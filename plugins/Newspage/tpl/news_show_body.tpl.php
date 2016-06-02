<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
    <div id="news_container" class="newsrow">
        <div  class="clear bodysize page">
<?php
if(isset($tpldata['NEWS_MSG']) && !empty($tpldata['NEWS_MSG'])) {
?>
            <div class="news_msg"><p><?php print $tpldata['NEWS_MSG']?></p></div>
<?php } ?>
            <div class="article_body">
                <?php isset($tpldata['NEWS_ADMIN_NAV']) ? print $tpldata['NEWS_ADMIN_NAV']:false ?>                                
                <?php isset($tpldata['ADD_TO_NEWSSHOW_TOP']) ? print $tpldata['ADD_TO_NEWSSHOW_TOP']:false ?>

                <div class="article_title">
                    <?php isset($tpldata['NEWS_TITLE']) ? print $tpldata['NEWS_TITLE']:false ?>
                </div>
                <div class="extra-small"><?php print($tpldata['NEWS_DATE'])?> | <?php print $tpldata['NEWS_AUTHOR'] ?></div>                
                <div class="article_lead">
                    <?php isset($tpldata['NEWSLEAD']) ? print $tpldata['NEWS_LEAD']:false ?>
                </div>                                
                <hr/>
<?php if(!empty($tpldata['NEWS_MAIN_MEDIA'])) {
?>
                <div class="article_main_media">
                   <?php isset($tpldata['NEWS_MAIN_MEDIA']) ? print $tpldata['NEWS_MAIN_MEDIA']:false ?>
                </div>                
<?php } ?>                
                <div class="article_text">
                    <?php isset($tpldata['NEWS_TEXT']) ? print $tpldata['NEWS_TEXT']:false ?>
                </div>      
                <?php isset($tpldata['ADD_TO_NEWSHOW_BOTTOM']) ? print $tpldata['ADD_TO_NEWSSHOW_BOTTOM']:false ?>
            </div>
            <div class="article_side">
                <?php isset($tpldata['ADD_TO_NEWS_SIDE']) ? print $tpldata['ADD_TO_NEWSHOW_SIDE']:false ?>
            </div>
        </div>
    </div>