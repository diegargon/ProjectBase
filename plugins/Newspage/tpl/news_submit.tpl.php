<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<div  class="clear bodysize page">
    <div class="standard_box submit_box">        
        <form  id="form_sendnews" action="" autocomplete="on" method="post">         
        <section>
        <h1><?php print $LANGDATA['L_SEND_NEWS']?></h1>  
        <div class="news_submit_center_wrapper">
        <div class="submit_items">
            <p> 
                <label for="news_author"><?php print $LANGDATA['L_NEWS_AUTHOR'] ?> </label>
                <input <?php print $data['can_change_author'] ?>   id="news_author" name="news_author" required="required" type="text"  maxlength="13" value="<?php print $data['username'] ?>"/>
            </p>            
        </div>
        <div class="submit_items">
            <p> 
                <label for="news_title"><?php print $LANGDATA['L_NEWS_TITLE'] ?> </label>
                <input value="<?php isset($data['post_title']) ? print $data['post_title'] : false ?>"  minlength="<?php print $config['NEWS_TITLE_MIN_LENGHT']?>" maxlength="<?php print $config['NEWS_TITLE_MAX_LENGHT']?>" id="news_title" name="news_title" required="required" type="text" placeholder=""/>
            </p>
        </div>
        <div class="submit_items">
            <p> 
                <label for="news_lead"><?php print $LANGDATA['L_NEWS_LEAD'] ?> </label>                
                <textarea required="required"  minlength="<?php print $config['NEWS_LEAD_MIN_LENGHT']?>" maxlength="<?php print $config['NEWS_LEAD_MAX_LENGHT']?>" id="news_lead" name="news_lead" ><?php isset($data['post_lead']) ? print $data['post_lead'] : false ?></textarea>                
            </p>            
        </div>
        <div class="submit_items">
            <p> 
                <label for="news_text"><?php print $LANGDATA['L_NEWS_TEXT'] ?> </label>
                <textarea required="required"  minlength="<?php print $config['NEWS_TEXT_MIN_LENGHT']?>" maxlength="<?php print $config['NEWS_TEXT_MAX_LENGHT']?>" id="news_text" name="news_text"><?php isset($data['post_text']) ? print $data['post_text'] : false ?></textarea>
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
                <input type="checkbox" name="news_featured" id="news_featured" value="1"/>

<?php                
}
?>
            </p>                
        </div>           
        <div class="submit_buttom">
            <p> 
                <input type="submit" id="sendnews" name="sendnews" class="btnSubmitNews" value="<?php print $LANGDATA['L_SEND_NEWS']?>" /> 
            </p>             
        </div>   
        </div>                     
        </section>
            </form>       
    </div>       
</div>
