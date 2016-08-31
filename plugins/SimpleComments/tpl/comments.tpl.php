<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

if (!empty($data['TPL_FIRST'])) {
    ?>
        <div id="comments">
            <section>
                <h2><?php print $LANGDATA['L_SC_COMMENTS'] ?></h2>
            <?php } ?>
            <div class="comment">
                <span class="avatar">
                    <a href="<?php print $data['p_url'] ?>"><img width="35"  src="<?php print $data['avatar'] ?>" alt="" /></a>
                </span>

                <span class="c_author"><a href="<?php print $data['p_url'] ?>"><?php print $data['username'] ?></a></span>
                <span class="c_date"><?php print format_date($data['date']) ?></span>
<?php !empty($data['COMMENT_EXTRA']) ? print $data['COMMENT_EXTRA'] : false; ?>
                    <p class="comment_body"><?php print $data['message'] ?></p>
<?php !empty($data['COMMENT_POST_MESSAGE_EXTRA']) ? print $data['COMMENT_POST_MESSAGE_EXTRA'] : false; ?>                    
            </div>
<?php if (!empty($data['TPL_LAST'])) { ?>
            </section>
        </div>
    <?php
}