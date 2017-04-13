<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div>
    <form id="form_comment_rate[<?= $data['cid'] ?>]" method="post" action="#">
        <input type="hidden" class="rate_rid" name="rate_rid" value="<?= $data['cid'] ?>" />
        <input type="hidden" class="rate_lid" name="rate_lid" value="<?= $data['lang_id'] ?>" />
        &nbsp;
        <button <?= !empty($data['btnExtra']) ? $data['btnExtra'] : null ?>  class="btnCommentRate
                <?= !empty($data['show_pointer']) ? "show_pointer" : null ?> <?= $data['stars1'] ?>" title="1" value="1" type="button">
        </button>
        <button <?= !empty($data['btnExtra']) ? $data['btnExtra'] : null ?> class="btnCommentRate
                <?= !empty($data['show_pointer']) ? "show_pointer" : null?> <?= $data['stars2'] ?>" title="2" value="2" type="button">
        </button>
        <button <?= !empty($data['btnExtra']) ?  $data['btnExtra'] : null ?> class="btnCommentRate
                <?= !empty($data['show_pointer']) ? "show_pointer" : null ?> <?= $data['stars3'] ?>" title="3" value="3" type="button">
        </button>
        <button <?= !empty($data['btnExtra']) ?  $data['btnExtra'] : null ?> class="btnCommentRate
                <?= !empty($data['show_pointer']) ? "show_pointer" : null ?> <?= $data['stars4'] ?>" title="4" value="4" type="button">
        </button>
        <button <?= !empty($data['btnExtra']) ? $data['btnExtra'] : null ?> class="btnCommentRate
                <?= !empty($data['show_pointer']) ? "show_pointer" : null ?> <?= $data['stars5'] ?>" title="5" value="5" type="button">
        </button>
    </form>
</div>