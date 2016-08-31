<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">
    <div class="standard_box submit_box">
        <form  id="form_news" action="" autocomplete="on" method="post">
            <section>
                <h1><?php print $data['news_form_title'] ?></h1>
                <div class="news_submit_center_wrapper">
                    <?php !empty($tpldata['NEWS_FORM_TOP_OPTION']) ? print $tpldata['NEWS_FORM_TOP_OPTION'] : false; ?>
                    <div class="submit_items">
                        <p>
                            <label for="news_author"><?php print $LANGDATA['L_NEWS_AUTHOR'] ?> </label>
                            <input <?php !empty($data['can_change_author']) ? print $data['can_change_author'] : false; ?>   id="news_author" name="news_author" required="required" type="text"  maxlength="13" value="<?php print $data['author'] ?>"/>
                            <?php if (!empty($data['can_change_author'])) { ?>
                                <input  name="news_author"  type="hidden" value="<?php print $data['author'] ?>"/>
                            <?php } ?>
                        </p>
                    </div>
                    <?php if (!empty($data['translator'])) { ?>
                        <div class="submit_items">
                            <p>
                                <label for="news_translator"><?php print $LANGDATA['L_NEWS_TRANSLATOR'] ?> </label>
                                <input <?php !empty($data['can_change_author']) ? print $data['can_change_author'] : false; ?> id="news_translator" name="news_translator"  type="text"  maxlength="13" value="<?php print $data['translator'] ?>"/>
                                <input  name="news_translator_id"  type="hidden" value="<?php print $data['translator_id'] ?>"/>
                                <?php if (!empty($data['can_change_author'])) { ?>
                                    <input  name="news_translator"  type="hidden" value="<?php print $data['translator'] ?>"/>                                    
                                <?php } ?>
                            </p>
                        </div>
                    <?php } ?>
                    <div class="submit_items">
                        <p>
                            <label for="news_title"><?php print $LANGDATA['L_NEWS_TITLE'] ?> </label>
                            <input value="<?php isset($data['title']) ? print $data['title'] : false ?>"  minlength="<?php print $config['NEWS_TITLE_MIN_LENGHT'] ?>" maxlength="<?php print $config['NEWS_TITLE_MAX_LENGHT'] ?>" id="news_title" name="news_title" required="required" type="text" placeholder=""/>
                        </p>
                    </div>
                    <div class="submit_items">
                        <label for="news_lead"><?php print $LANGDATA['L_NEWS_LEAD'] ?> </label>
                        <textarea required="required"  minlength="<?php print $config['NEWS_LEAD_MIN_LENGHT'] ?>" maxlength="<?php print $config['NEWS_LEAD_MAX_LENGHT'] ?>" id="news_lead" name="news_lead" ><?php isset($data['lead']) ? print $data['lead'] : false ?></textarea>
                    </div>
                    <div class="submit_items">
                        <label for="news_text"><?php print $LANGDATA['L_NEWS_TEXT'] ?> </label>
                        <?php !empty($data['news_text_bar']) ? print $data['news_text_bar'] : false; ?>
                        <textarea required="required"  minlength="<?php print $config['NEWS_TEXT_MIN_LENGHT'] ?>" maxlength="<?php print $config['NEWS_TEXT_MAX_LENGHT'] ?>" id="news_text" name="news_text" ><?php isset($data['text']) ? print $data['text'] : false ?></textarea>
                        <div id="EditorBtnBottomContainer">
                            <input class="btnPreview" type='button' id="btnShowPreview" value="<?php print $LANGDATA['L_NEWS_PREVIEW'] ?>"/>
                            <input class="btnPreview" type='button' id="btnHiddePreview" value="<?php print $LANGDATA['L_NEWS_HIDDE_PREVIEW'] ?>"/>
                        </div>
                        <div id="preview"></div>
                    </div>
                    <?php !empty($tpldata['NEWS_FORM_MIDDLE_OPTION']) ? print $tpldata['NEWS_FORM_MIDDLE_OPTION'] : false; ?>
                    
                    <?php if ($config['NEWS_SOURCE'] && !empty($data['news_auth']) && $data['news_auth'] != "translator") { ?>
                        <div class="submit_items">
                            <p> 
                                <label for="news_source"><?php print $LANGDATA['L_NEWS_SOURCE'] ?> </label>
                                <input  value="<?php isset($data['news_source']) ? print $data['news_source'] : false ?>"  minlength="<?php print $config['NEWS_LINK_MIN_LENGHT'] ?>" maxlength="<?php print $config['NEWS_LINK_MAX_LENGHT'] ?>" id="news_source" class="news_link" name="news_source" type="text" placeholder="http://site.com"/>
                            </p>
                        </div>
                    <?php } ?>
                    <?php if ($config['NEWS_RELATED'] && !empty($data['news_auth']) && $data['news_auth'] != "translator") { ?>
                        <div class="submit_items">
                            <p>
                                <label for="news_new_related"><?php print $LANGDATA['L_NEWS_RELATED'] ?> </label>
                                <input  value="<?php isset($data['news_new_related']) ? print $data['news_new_related'] : false ?>"  minlength="<?php print $config['NEWS_LINK_MIN_LENGHT'] ?>" maxlength="<?php print $config['NEWS_LINK_MAX_LENGHT'] ?>" id="news_new_related" class="news_link" name="news_new_related" type="text" placeholder="http://site.com"/>
                                <?php isset($data['news_related']) ? print $data['news_related'] : false ?>
                            </p>
                        </div>
                    <?php } ?>
                    <?php !empty($tpldata['NEWS_FORM_BOTTOM_OPTION']) ? print $tpldata['NEWS_FORM_BOTTOM_OPTION'] : false; ?>
                    <div class="submit_items">
                        <p>
                            <span class="submit_others_label"><?php print $LANGDATA['L_NEWS_OTHER_OPTIONS'] ?> </span>
                            <?php if (!empty($data['select_categories'])) { ?>
                                <span  class="lang_label"><?php print $LANGDATA['L_NEWS_CATEGORY'] ?></span>
                                <?php print $data['select_categories'] ?>
                            <?php } ?>
                            <?php if (defined('MULTILANG') && !empty($data['select_langs'])) { ?>
                                <span  class="lang_label"><?php print $LANGDATA['L_NEWS_LANG'] ?></span>
                                <?php print $data['select_langs'] ?>
                            <?php } ?>
                            <?php if (!empty($data['select_acl'])) { ?>
                                <span  class="acl_label"><?php print $LANGDATA['L_ACL'] ?></span>
                                <?php print $data['select_acl'] ?>
                                <span  class="featured_label"><?php print $LANGDATA['L_NEWS_FEATURED'] ?></span>
                                <input <?php !empty($data['featured']) ? print "checked" : false ?> type="checkbox" name="news_featured" id="news_featured" value="1"/>
                            <?php } ?>
                        </p>
                        <?php !empty($tpldata['NEWS_FORM_BOTTOM_OTHER_OPTION']) ? print $tpldata['NEWS_FORM_BOTTOM_OTHER_OPTION'] : false; ?>
                        <p>
                            <a href="/terms.php" target="_blank"><?php print $LANGDATA['L_TOS'] ?></a><input <?php !empty($data['tos_checked']) ? print "checked" : false ?> id="tos" name="tos" required="required" type="checkbox"/>
                        </p>
                    </div>
                    <div class="submit_buttom">
                        <p>
                            <input type="submit" id="newsFormSubmit" name="newsFormSubmit" class="btnSubmitForm" value="<?php print $LANGDATA['L_SEND_NEWS'] ?>" />
                        </p>
                    </div>
                </div>
            </section>
        </form>
    </div>
</div>