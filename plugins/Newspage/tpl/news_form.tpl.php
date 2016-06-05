<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<div  class="clear bodysize page">
    <div class="standard_box submit_box">        
        <form  id="form_news" action="" autocomplete="on" method="post">         
        <section>
        <h1><?php print  $data['NEWS_FORM_TITLE'] ?></h1>  
        <div class="news_submit_center_wrapper">
        <div class="submit_items">
            <p> 
                <label for="news_author"><?php print $LANGDATA['L_NEWS_AUTHOR'] ?> </label>
                <input <?php print $data['can_change_author'] ?>   id="news_author" name="news_author" required="required" type="text"  maxlength="13" value="<?php print $data['author'] ?>"/>
            </p>            
        </div>
        <div class="submit_items">
            <p> 
                <label for="news_title"><?php print $LANGDATA['L_NEWS_TITLE'] ?> </label>
                <input value="<?php isset($data['title']) ? print $data['title'] : false ?>"  minlength="<?php print $config['NEWS_TITLE_MIN_LENGHT']?>" maxlength="<?php print $config['NEWS_TITLE_MAX_LENGHT']?>" id="news_title" name="news_title" required="required" type="text" placeholder=""/>
            </p>
        </div>
        <div class="submit_items">
            <p> 
                <label for="news_lead"><?php print $LANGDATA['L_NEWS_LEAD'] ?> </label>                
                <textarea required="required"  minlength="<?php print $config['NEWS_LEAD_MIN_LENGHT']?>" maxlength="<?php print $config['NEWS_LEAD_MAX_LENGHT']?>" id="news_lead" name="news_lead" ><?php isset($data['lead']) ? print $data['lead'] : false ?></textarea>                
            </p>            
        </div>
        <div class="submit_items">
            <p> 
                <label for="news_text"><?php print $LANGDATA['L_NEWS_TEXT'] ?> </label>
                <textarea required="required"  minlength="<?php print $config['NEWS_TEXT_MIN_LENGHT']?>" maxlength="<?php print $config['NEWS_TEXT_MAX_LENGHT']?>" id="news_text" name="news_text"><?php isset($data['text']) ? print $data['text'] : false ?></textarea>
            </p>
        </div>
        <div class="submit_items">
            <p> 
                <label for="news_main_media"><?php print $LANGDATA['L_NEWS_MAIN_MEDIA'] ?> </label>
                <input value="<?php isset($data['main_media']) ? print $data['main_media'] : false ?>"  minlength="<?php print $config['NEWS_MEDIA_MIN_LENGHT']?>" maxlength="<?php print $config['NEWS_MEDIA_MAX_LENGHT']?>" id="news_main_media" name="news_main_media" type="text" placeholder="http://site.com/image.jpg"/>
            </p>
        </div>            
        <div class="submit_items">
            <p> 
                <span class="submit_others_label"><?php print $LANGDATA['L_NEWS_OTHER_OPTIONS'] ?> </span>
                <span  class="lang_label"><?php print $LANGDATA['L_NEWS_CATEGORY']?></span>
                    <?php print $data['select_categories'] ?>
                
<?php
if(defined('MULTILANG')) {
?>                
                <span  class="lang_label"><?php print $LANGDATA['L_NEWS_LANG']?></span>
                    <?php print $data['select_langs'] ?>
<?php            
}
?>        
<?php 
if(!empty($data['select_acl'])) {
?>
                <span  class="acl_label"><?php print $LANGDATA['L_ACL']?></span>
                    <?php print $data['select_acl'] ?>

                <span  class="featured_label"><?php print $LANGDATA['L_NEWS_FEATURED']?></span>
                <input <?php  !empty($data['featured']) ? print "checked": false ?> type="checkbox" name="news_featured" id="news_featured" value="1"/>

<?php                
}
?>
            </p>         
            <input type="hidden" value="<?php  !empty($data['update']) ? print $data['update']: false ?>"  name="news_update" id="news_update"/> 
            <input type="hidden" value="<?php  !empty($data['current_langid']) ? print $data['current_langid']: false ?>"  name="news_current_langid" id="news_current_langid"/> 
        </div>                       
        <div class="submit_buttom">
            <p> 
                <input type="submit" id="newsFormSubmit" name="newsFormSubmit" class="btnSubmitForm" value="<?php print $LANGDATA['L_SEND_NEWS']?>" /> 
            </p>             
        </div>   
        </div>                     
        </section>
            </form>       
    </div>       
</div>
