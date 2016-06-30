<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
        <div class="submit_items">
                <label><?php print $LANGDATA['L_NEWS_EXTRA_MEDIA'] ?> </label>
                <div class="center">
                    <button  class="btnAddField" id='btnAddField'><?php print $LANGDATA['L_NEWS_ADDFIELD']?> </button> 
                </div>
                <div id='extra_input'>
<?php !empty($data['extra_media']) ? print $data['extra_media'] : false ?>
                </div>
        </div> 