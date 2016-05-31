<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<div  class="clear bodysize page">
    <div class="standard_box submit_box">        
        <form  action="" autocomplete="on" method="post">         
        <section>
        <h1><?php print $LANGDATA['L_SEND_NEWS']?></h1>  
        <div class="news_submit_center_wrapper">
        <div class="submit_items">
            <p> 
                <label for="news_author"><?php print $LANGDATA['L_NEWS_AUTHOR'] ?> </label>
                <input <?php print $data['can_change_author'] ?>  class="submit_author" id="news_author" name="news_author" required="required" type="text"  maxlength="13" value="<?php print $data['username'] ?>"/>
            </p>            
        </div>
        <div class="submit_items">
            <p> 
                <label for="news_title"><?php print $LANGDATA['L_NEWS_TITLE'] ?> </label>
                <input class="submit_title" required="required" minlength="<?php $config['NEWS_TITLE_MIN_LENGHT']?>" maxlength="<?php print $config['NEWS_TITLE_MAX_LENGHT']?>" id="news_title" name="news_title" required="required" type="text" placeholder=""/>
            </p>
        </div>
        <div class="submit_items">
            <p> 
                <label for="news_lead"><?php print $LANGDATA['L_NEWS_LEAD'] ?> </label>                
                <textarea required="required" class="submit_lead" minlength=""<?php $config['NEWS_LEAD_MIN_LENGHT']?>"" maxlength="<?php $config['NEWS_LEAD_MAX_LENGHT']?>" id="news_lead" name="news_lead" ></textarea>                
            </p>            
        </div>
        <div class="submit_items">
            <p> 
                <label for="news_text"><?php print $LANGDATA['L_NEWS_TEXT'] ?> </label>
                <textarea required="required" class="submit_text" minlength=""<?php $config['NEWS_TEXT_MIN_LENGHT']?>"" maxlength="<?php $config['NEWS_TEXT_MAX_LENGHT']?>" id="news_text" name="news_text"></textarea>
            </p>
        </div>
<?php
if(defined('MULTILANG')) {
?>
        <div class="submit_items">
            <p> 
                <span class="submit_others_label"><?php print $LANGDATA['L_NEWS_OTHER_OPTIONS'] ?> </span>
                <span class="lang_label"><?php print $LANGDATA['L_NEWS_LANG']?></span>
                    <?php print $data['select_langs'] ?>
            </p>
        </div>           
<?php            
}
?>            
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
