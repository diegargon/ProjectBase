<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div>
    <form id="form_news_rate" method="post" action="#">
        <input type="hidden" class="rate_rid" name="rate_rid" value="<?= $data['nid'] ?>" />
        <input type="hidden" class="rate_lid" name="rate_lid" value="<?= $data['lang_id'] ?>" />
        &nbsp;
        <button <?php !empty($data['btnExtra']) ? print $data['btnExtra'] : false ?> class="btnNewsRate 
                <?php !empty($data['show_pointer']) ? print "show_pointer" : false ?> <?= $data['stars1'] ?>" title="1" value="1" type="button">
        </button>
        <button <?php !empty($data['btnExtra']) ? print $data['btnExtra'] : false ?> class="btnNewsRate 
                <?php !empty($data['show_pointer']) ? print "show_pointer" : false ?> <?= $data['stars2'] ?>" title="2" value="2" type="button">
        </button>
        <button <?php !empty($data['btnExtra']) ? print $data['btnExtra'] : false ?> class="btnNewsRate 
                <?php !empty($data['show_pointer']) ? print "show_pointer" : false ?> <?= $data['stars3'] ?>" title="3" value="3" type="button">
        </button>
        <button <?php !empty($data['btnExtra']) ? print $data['btnExtra'] : false ?> class="btnNewsRate 
                <?php !empty($data['show_pointer']) ? print "show_pointer" : false ?> <?= $data['stars4'] ?>" title="4" value="4" type="button">
        </button>
        <button <?php !empty($data['btnExtra']) ? print $data['btnExtra'] : false ?> class="btnNewsRate 
                <?php !empty($data['show_pointer']) ? print "show_pointer" : false ?> <?= $data['stars5'] ?>" title="5" value="5" type="button">
        </button>
    </form>
</div>