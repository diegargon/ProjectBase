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
                <?php isset($tpldata['ADD_TO_NEWSSHOW_TOP']) ? print $tpldata['ADD_TO_NEWSSHOW_TOP']:false ?>
            <div class="article_body">
<?php  if(!empty($tpldata['NEWS_ADMIN_NAV'])) { ?>
                <div id='adm_nav_container'>
                <nav id='adm_nav'>
                    <ul>
                        <?php print $tpldata['NEWS_ADMIN_NAV'] ?>
                    </ul>
                </nav>
                </div>
<?php } ?>
                <div class="article_title">
                    <?php isset($tpldata['news_title']) ? print $tpldata['news_title']:false ?>
                </div>
                <div class="extra-small">
                    <?php !empty($tpldata['author_avatar']) ? print $tpldata['author_avatar'] : false; ?>
                    <?php print($tpldata['news_date'])?> | 
                    <a href='/profile.php?lang=<?php print $config['WEB_LANG']?>&viewprofile=<?php print $tpldata['news_author_uid'] ?>'>
                    <?php print $tpldata['news_author'] ?>
                    </a> 
                    <?php 
                        isset($tpldata['news_translator']) ? print " | ". $LANGDATA['L_NEWS_TRANSLATE_BY'] . $tpldata['news_translator'] : false ; 
                    ?>
<?php if(isset($tpldata['NEWS_SOURCE'])) { ?>
                    | <span><?php print $LANGDATA['L_NEWS_SOURCE'] .": ";  print $tpldata['NEWS_SOURCE'] ?> </span>
<?php } ?>
                </div>
                <p class="article_lead">
                    <?php isset($tpldata['news_lead']) ? print $tpldata['news_lead']:false ?>
                </p>
                <hr/>
                <?php !empty($tpldata['news_main_pre_text']) ? print $tpldata['news_main_pre_text'] : false; ?>
                <div class="article_text">
                    <?php isset($tpldata['news_text']) ? print $tpldata['news_text']:false ?>
                </div>
                <?php !empty($tpldata['news_main_after_text']) ? print $tpldata['news_main__pre_text'] : false; ?>
<?php if(!empty($tpldata['NEWS_RELATED'])) {
?>
                <div class="related">
                    <ul>
                   <?php  print $tpldata['NEWS_RELATED'] ?>
                    </ul>
                </div>
<?php } ?>
                <?php isset($tpldata['ADD_TO_NEWSSHOW_BOTTOM']) ? print $tpldata['ADD_TO_NEWSSHOW_BOTTOM'] : false ?>
            </div>
            <div class="article_side">
                <?php !empty($tpldata['ADD_TO_NEWS_SIDE']) ? print $tpldata['ADD_TO_NEWS_SIDE'] : false ?>
            </div>
        </div>
    </div>