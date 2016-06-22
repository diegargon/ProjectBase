<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<p><a href='#new_comment_wrap' class='btnShow'><?php print $LANGDATA['L_SC_NEW_COMMENT']?></a></p>
<div id="new_comment_wrap">        
        <form method='post' action='' id='form_new_comment'>
            <textarea id='new_comment' name="news_comment" ></textarea>
            <input id="btnSendNewComment" name="btnSendNewComment" type="submit" value="<?php print $LANGDATA['L_SEND'] ?>" />
        </form>
</div>        