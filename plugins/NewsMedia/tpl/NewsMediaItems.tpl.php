<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
        <div class="submit_items">
            <p>
                <label for="news_main_media"><?php print $LANGDATA['L_NEWS_MAIN_MEDIA'] ?> </label>
                <input <?php !empty($data['post_newlang']) ? print "disabled": false ?> 
                    value="<?php isset($data['main_media']) ? print $data['main_media'] : false ?>"  
                    minlength="<?php print $config['NEWS_MEDIA_MIN_LENGHT']?>" 
                    maxlength="<?php print $config['NEWS_MEDIA_MAX_LENGHT']?>" 
                    id="news_main_media" class="news_link" name="news_main_media" 
                    type="text" placeholder="http://site.com/image.jpg"/>
                <?php if (!empty($data['post_newlang'])) { ?>
                <input  value="<?php isset($data['main_media']) ? print $data['main_media'] : false ?>"  class="news_link" name="news_main_media" type="hidden" />
                <?php } ?>                
            </p>
        </div> 