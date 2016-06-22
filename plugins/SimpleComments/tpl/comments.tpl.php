<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

if( !empty($data['TPL_FIRST'])) {?>
<div id="comments">
    <section>
        <h2><?php print $LANGDATA['L_SC_COMMENTS']?></h2>
<?php }?>
            <div id="comment">
                <p><span class="c_author"><?php print $data['author']?></span><span class="c_date"><?php print format_date($data['date'])?></span></p>
                <p><?php print $data['message']?></p>
            </div>
<?php if(!empty($data['TPL_LAST'])) {?>
    </section>
</div>
<?php } 
