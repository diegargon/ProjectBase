<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">
    <div class="standard_box submit_box">
        <form  id="form_news" action="#" autocomplete="on" method="post">
            <section>
                <h1><?= $data['news_form_title'] ?></h1>
                <div class="news_submit_center_wrapper">
                    <?= !empty($tpldata['NEWS_FORM_TOP_OPTION']) ? $tpldata['NEWS_FORM_TOP_OPTION'] : null ?>
                    <div class="submit_items">
                        <p>
                            <label for="news_author"><?= $LANGDATA['L_NEWS_AUTHOR'] ?> </label>
                            <input <?= !empty($data['can_change_author']) ? $data['can_change_author'] : null ?>   id="news_author" name="news_author" required="required" type="text"  maxlength="13" value="<?= $data['author'] ?>"/>
                            <?php if (!empty($data['can_change_author'])) { ?>
                                <input  name="news_author"  type="hidden" value="<?= $data['author'] ?>"/>
                            <?php } ?>
                        </p>
                    </div>
                    <?php if (!empty($data['translator'])) { ?>
                        <div class="submit_items">
                            <p>
                                <label for="news_translator"><?= $LANGDATA['L_NEWS_TRANSLATOR'] ?> </label>
                                <input <?= !empty($data['can_change_author']) ? $data['can_change_author'] : null ?> id="news_translator" name="news_translator"  type="text"  maxlength="13" value="<?= $data['translator'] ?>"/>
                                <input  name="news_translator_id"  type="hidden" value="<?= $data['translator_id'] ?>"/>
                                <?php if (!empty($data['can_change_author'])) { ?>
                                    <input  name="news_translator"  type="hidden" value="<?= $data['translator'] ?>"/>                                    
                                <?php } ?>
                            </p>
                        </div>
                    <?php } ?>
                    <div class="submit_items">
                        <p>
                            <label for="news_title"><?= $LANGDATA['L_NEWS_TITLE'] ?> </label>
                            <input value="<?= isset($data['title']) ? $data['title'] : null ?>"  minlength="<?= $config['NEWS_TITLE_MIN_LENGHT'] ?>" maxlength="<?= $config['NEWS_TITLE_MAX_LENGHT'] ?>" id="news_title" name="news_title" required="required" type="text" placeholder=""/>
                        </p>
                    </div>
                    <div class="submit_items">
                        <label for="news_lead"><?= $LANGDATA['L_NEWS_LEAD'] ?> </label>
                        <textarea required="required"  minlength="<?= $config['NEWS_LEAD_MIN_LENGHT'] ?>" maxlength="<?= $config['NEWS_LEAD_MAX_LENGHT'] ?>" id="news_lead" name="news_lead" ><?= isset($data['lead']) ? $data['lead'] : null ?></textarea>
                    </div>
                    <div class="submit_items">
                        <label for="news_text"><?= $LANGDATA['L_NEWS_TEXT'] ?> </label>
                        <?= !empty($data['news_text_bar']) ? $data['news_text_bar'] : null ?>
                        <textarea required="required"  minlength="<?= $config['NEWS_TEXT_MIN_LENGHT'] ?>" maxlength="<?= $config['NEWS_TEXT_MAX_LENGHT'] ?>" id="news_text" name="news_text" ><?= isset($data['text']) ? $data['text'] : null ?></textarea>
                        <div id="EditorBtnBottomContainer">
                            <input class="btnPreview" type='button' id="btnShowPreview" value="<?= $LANGDATA['L_NEWS_PREVIEW'] ?>"/>
                            <input class="btnPreview" type='button' id="btnHiddePreview" value="<?= $LANGDATA['L_NEWS_HIDDE_PREVIEW'] ?>"/>
                        </div>
                        <div id="preview"></div>
                    </div>
                    <?= !empty($tpldata['NEWS_FORM_MIDDLE_OPTION']) ? $tpldata['NEWS_FORM_MIDDLE_OPTION'] : null ?>
                    
                    <?php if ($config['NEWS_SOURCE'] && !empty($data['news_auth']) && $data['news_auth'] != "translator") { ?>
                        <div class="submit_items">
                            <p> 
                                <label for="news_source"><?= $LANGDATA['L_NEWS_SOURCE'] ?> </label>
                                <input  value="<?= isset($data['news_source']) ? $data['news_source'] : null ?>"  minlength="<?= $config['NEWS_LINK_MIN_LENGHT'] ?>" maxlength="<?= $config['NEWS_LINK_MAX_LENGHT'] ?>" id="news_source" class="news_link" name="news_source" type="text" placeholder="http://site.com"/>
                            </p>
                        </div>
                    <?php } ?>
                    <?php if ($config['NEWS_RELATED'] && !empty($data['news_auth']) && $data['news_auth'] != "translator") { ?>
                        <div class="submit_items">
                            <p>
                                <label for="news_new_related"><?= $LANGDATA['L_NEWS_RELATED'] ?> </label>
                                <input  value="<?= isset($data['news_new_related']) ? $data['news_new_related'] : null ?>"  minlength="<?= $config['NEWS_LINK_MIN_LENGHT'] ?>" maxlength="<?= $config['NEWS_LINK_MAX_LENGHT'] ?>" id="news_new_related" class="news_link" name="news_new_related" type="text" placeholder="http://site.com"/>
                                <?= isset($data['news_related']) ? $data['news_related'] : null ?>
                            </p>
                        </div>
                    <?php } ?>
                    <?php !empty($tpldata['NEWS_FORM_BOTTOM_OPTION']) ? $tpldata['NEWS_FORM_BOTTOM_OPTION'] : null ?>
                    <div class="submit_items">
                        <p>
                            <span class="submit_others_label"><?= $LANGDATA['L_NEWS_OTHER_OPTIONS'] ?> </span>
                            <?php if (!empty($data['select_categories'])) { ?>
                                <span  class="lang_label"><?= $LANGDATA['L_NEWS_CATEGORY'] ?></span>
                                <?= $data['select_categories'] ?>
                            <?php } ?>
                            <?php if (defined('MULTILANG') && !empty($data['select_langs'])) { ?>
                                <span  class="lang_label"><?= $LANGDATA['L_NEWS_LANG'] ?></span>
                                <?= $data['select_langs'] ?>
                            <?php } ?>
                            <?php if (!empty($data['select_acl'])) { ?>
                                <span  class="acl_label"><?= $LANGDATA['L_ACL'] ?></span>
                                <?= $data['select_acl'] ?>
                                <span  class="featured_label"><?= $LANGDATA['L_NEWS_FEATURED'] ?></span>
                                <input <?= !empty($data['featured']) ? "checked" : false ?> type="checkbox" name="news_featured" id="news_featured" value="1"/>
                            <?php } ?>
                        </p>
                        <?= !empty($tpldata['NEWS_FORM_BOTTOM_OTHER_OPTION']) ?  $tpldata['NEWS_FORM_BOTTOM_OTHER_OPTION'] : null ?>
                        <p>
                            <a href="<?= $data['terms_url'] ?>" target="_blank"><?= $LANGDATA['L_TOS'] ?></a>
                            <input <?= !empty($data['tos_checked']) ?  "checked" : null ?> id="tos" name="tos" required="required" type="checkbox"/>
                        </p>
                    </div>
                    <div class="submit_buttom">
                        <p>
                            <input type="submit" id="newsFormSubmit" name="newsFormSubmit" class="btnSubmitForm" value="<?= $LANGDATA['L_SEND_NEWS'] ?>" />
                        </p>
                    </div>
                </div>
            </section>
        </form>
    </div>
</div>