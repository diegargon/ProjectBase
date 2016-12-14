<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div id="news_container" class="newsrow">
    <div  class="clear bodysize page">
        <?php
        if (!empty($data['news_msg']) && !empty($data['news_msg'])) {
            ?>
            <div class="news_msg"><p><?php print $data['news_msg'] ?></p></div>
            <?php
        }
        !empty($tpldata['ADD_TO_NEWSSHOW_TOP']) ? print $tpldata['ADD_TO_NEWSSHOW_TOP'] : false;
        if (!empty($data['NEWS_BREADCRUMB'])) {
            ?>
            <div id='news_breadcrumb'>
                <ul class='breadcrumb'>
                    <?php print $data['NEWS_BREADCRUMB'] ?>
                </ul>
            </div>
            <?php
        }        
        ?>
        <section class="article_body">
            <h1>
                <?php !empty($data['title']) ? print $data['title'] : false ?>
            </h1>
            <div>                
                <a rel="nofollow" href="https://www.facebook.com/dialog/share?app_id=1481492868545684&display=popup&href=<?php print "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']?>&redirect_uri=<?php print "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']?>" target="_blank"><img width="19" height="19" src="<?php echo $config['STATIC_SRV_URL'] . '/plugins/Newspage/tpl/images/Facebook.png' ?>" alt="facebook" /></a>
                <a rel="nofollow" href="https://meneame.net/submit.php?url=<?php print "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']?>" target="_blank"><img width="19" height="19" src="<?php echo $config['STATIC_SRV_URL'] . '/plugins/Newspage/tpl/images/Meneame.png' ?>" alt="Meneame" /></a>
                <a rel="nofollow" href="https://twitter.com/share?url=<?php print "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']?>" target="_blank"><img width="19" height="19" src="<?php echo $config['STATIC_SRV_URL'] . '/plugins/Newspage/tpl/images/Twitter.png' ?>" alt="Tweeter" /></a>
                <a rel="nofollow" href="https://plus.google.com/share?url=<?php print "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']?>" target="_blank"><img width="19" height="19" src="<?php echo $config['STATIC_SRV_URL'] . '/plugins/Newspage/tpl/images/Google.png' ?>" alt="Google+" /></a>
                <a rel="nofollow" href="https://www.reddit.com/submit?url=<?php print "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']?>" target="_blank"><img width="19" height="19" src="<?php echo $config['STATIC_SRV_URL'] . '/plugins/Newspage/tpl/images/Reddit.png' ?>" alt="Reddit" /></a>
            </div>
            <?php if (!empty($data['news_admin_nav'])) { ?>
                <nav id='adm_nav'>
                    <ul>
                        <?php print $data['news_admin_nav'] ?>
                    </ul>
                </nav>
            <?php } ?>
            <div id="news_info">
                <?php if (!empty($data['author_avatar'])) { ?> 
                    <div class='avatar'><img width='50' src='<?php print $data['author_avatar']; ?>' alt='' /></div>                        
                <?php } ?>
                <?php !empty($tpldata['ADD_NEWS_INFO_POST_AVATAR']) ? print $tpldata['ADD_NEWS_INFO_POST_AVATAR'] : false ?>
                <div class="extra-small">
                    <?php print $data['date'] ?> <br/>
                    <a href='/<?php print $config['WEB_LANG'] ?>/profile&viewprofile=<?php print $data['author_uid'] ?>'><?php print $data['author'] ?></a>
                    <?php
                    !empty($data['translator']) ? print " | " . $LANGDATA['L_NEWS_TRANSLATE_BY'] . $data['translator'] : false;
                    ?>
                    <?php if (!empty($data['news_sources'])) { ?>
                        | <span><?php print $LANGDATA['L_NEWS_SOURCE'] . ": " . $data['news_sources'] ?> </span>
                    <?php } ?>
                </div>
                <?php !empty($tpldata['ADD_NEWS_INFO_BOTTOM']) ? print $tpldata['ADD_NEWS_INFO_BOTTOM'] : false ?>
            </div>
            <?php if (!empty($data['lead'])) { ?>
                <p class="article_lead">
                    <?php print $data['lead'] ?>
                </p>
            <?php } ?>
            <hr/>
            <?php !empty($tpldata['NEWS_MAIN_PRE_TEXT']) ? print $tpldata['NEWS_MAIN_PRE_TEXT'] : false; ?>
            <div class="article_text">
                <?php !empty($data['text']) ? print $data['text'] : false ?>
            </div>
            <?php !empty($tpldata['NEWS_MAIN_AFTER_TEXT']) ? print $tpldata['news_main__pre_text'] : false; ?>
            <?php if (!empty($data['news_related'])) {
                ?>
                <div class="related">
                    <ul>
                        <?php print $data['news_related'] ?>
                    </ul>
                </div>
            <?php } ?>
            <?php !empty($data['pager']) ? print $data['pager'] : false ?>
            <?php !empty($tpldata['ADD_TO_NEWSSHOW_BOTTOM']) ? print $tpldata['ADD_TO_NEWSSHOW_BOTTOM'] : false ?>                        
        </section>
        <div class="article_side">
            <?php !empty($tpldata['ADD_TO_NEWS_SIDE']) ? print $tpldata['ADD_TO_NEWS_SIDE'] : false ?>
        </div>
    </div>
</div>