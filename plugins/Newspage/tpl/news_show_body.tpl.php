<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<div id="news_container" class="newsrow">
    <div  class="clear bodysize page">
        <?php
        if (isset($data['news_msg']) && !empty($data['news_msg'])) {
            ?>
            <div class="news_msg"><p><?php print $data['news_msg'] ?></p></div>
        <?php } ?>
        <?php isset($tpldata['ADD_TO_NEWSSHOW_TOP']) ? print $tpldata['ADD_TO_NEWSSHOW_TOP'] : false ?>
        <section class="article_body">
            <h1>
                <?php isset($data['news_title']) ? print $data['news_title'] : false ?>
            </h1>                
            <?php if (!empty($data['news_admin_nav'])) { ?>
                <div id='adm_nav_container'>
                    <nav id='adm_nav'>
                        <ul>
                            <?php print $data['news_admin_nav'] ?>
                        </ul>
                    </nav>
                </div>
            <?php } ?>
            <div id="news_info">
                <?php if (!empty($data['author_avatar'])) { ?> 
                    <div class='avatar'><img width='50' src='<?php print $data['author_avatar']; ?>' alt='' /></div>                        
                <?php } ?>
                <?php isset($tpldata['ADD_NEWS_INFO_POST_AVATAR']) ? print $tpldata['ADD_NEWS_INFO_POST_AVATAR'] : false ?>
                <div class="extra-small">
                    <?php print $data['news_date'] ?> <br/>
                    <a href='/<?php print $config['WEB_LANG'] ?>/profile&viewprofile=<?php print $data['news_author_uid'] ?>'>
                        <?php print $data['news_author'] ?>
                    </a>
                    <?php
                    isset($data['news_translator']) ? print " | " . $LANGDATA['L_NEWS_TRANSLATE_BY'] . $data['news_translator'] : false;
                    ?>
                    <?php if (isset($data['news_sources'])) { ?>
                        | <span><?php
                            print $LANGDATA['L_NEWS_SOURCE'] . ": ";
                            print $data['news_sources']
                            ?> </span>
                    <?php } ?>
                </div>
                <?php isset($tpldata['ADD_NEWS_INFO_BOTTOM']) ? print $tpldata['ADD_NEWS_INFO_BOTTOM'] : false ?>
            </div>
            <?php if (!empty($data['news_lead'])) { ?>
                <p class="article_lead">
                    <?php isset($data['news_lead']) ? print $data['news_lead'] : false ?>
                </p>
            <?php } ?>
            <hr/>
            <?php !empty($tpldata['NEWS_MAIN_PRE_TEXT']) ? print $tpldata['NEWS_MAIN_PRE_TEXT'] : false; ?>
            <div class="article_text">
                <?php isset($data['news_text']) ? print $data['news_text'] : false ?>
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
            <?php isset($tpldata['ADD_TO_NEWSSHOW_BOTTOM']) ? print $tpldata['ADD_TO_NEWSSHOW_BOTTOM'] : false ?>                        
        </section>
        <div class="article_side">
            <?php !empty($tpldata['ADD_TO_NEWS_SIDE']) ? print $tpldata['ADD_TO_NEWS_SIDE'] : false ?>
        </div>
    </div>
</div>