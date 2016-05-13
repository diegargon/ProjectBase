<?php
global $tpldata;
 
/* 
 *  Copyright @ 2016 Diego Garcia
 */


?>


    <div id="news_container" class="newsrow">
        <div  class="clear bodysize page">   
            <div class="article_body">
                <?php isset($tpldata['ADD_TO_NEWSSHOW_TOP']) ? print $tpldata['ADD_TO_NEWSSHOW_TOP']:false ?>
                
                <div class="article_title">
                    <?php isset($tpldata['NEWS_TITLE']) ? print $tpldata['NEWS_TITLE']:false ?>
                </div>
                <div class="article_lead">
                    <?php isset($tpldata['NEWSLEAD']) ? print $tpldata['NEWS_LEAD']:false ?>
                </div>                                
                <hr/>
                <div class="article_main_media">
                    <img src="<?php isset($tpldata['NEWS_MAIN_MEDIA']) ? print $tpldata['NEWS_MAIN_MEDIA']:false ?>"/>
                </div>
                
                <div class="article_text">
                    <?php isset($tpldata['NEWS_TEXT']) ? print $tpldata['NEWS_TEXT']:false ?>
                </div>      

                <?php isset($tpldata['ADD_TO_NEWSHOW']) ? print $tpldata['ADD_TO_NEWSSHOW_TOP']:false ?>
            </div>
            <div class="article_side">
                <?php isset($tpldata['ADD_TO_NEWS_SIDE']) ? print $tpldata['ADD_TO_NEWSHOW_SIDE']:false ?>
            </div>
        </div>
    </div>


