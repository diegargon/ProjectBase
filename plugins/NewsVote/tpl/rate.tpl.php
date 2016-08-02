<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
    <div>
        <form id="form_rate" method="post" action="">
            <input type="hidden" id="rate_uid" name="rate_uid" value="<?php print $data['uid'] ?>" />
            <input type="hidden" id="rate_rid" name="rate_rid" value="<?php print $data['nid'] ?>" />
            <input type="hidden" id="rate_lid" name="rate_lid" value="<?php print $data['lang_id'] ?>" />
            &nbsp;
            <button <?php !empty($data['btnExtra']) ? print $data['btnExtra'] : false ?>  class="btnRate" title="rate 1" style="<?php print $data['rate_style']?>" value="1" type="button">
                <img width="20" src="http://projectbase.envigo.net/plugins/NewsVote/tpl/images/<?php print $data['stars1'] ?>" alt="rate 1" />
            </button>
            <button <?php !empty($data['btnExtra']) ? print $data['btnExtra'] : false ?> class="btnRate" title="rate 2" style="<?php print $data['rate_style']?>" value="2" type="button">
                <img width="20" src="http://projectbase.envigo.net/plugins/NewsVote/tpl/images/<?php print $data['stars2'] ?>" alt="rate 1" />
            </button>
            <button <?php !empty($data['btnExtra']) ? print $data['btnExtra'] : false ?> class="btnRate" title="rate 3" style="<?php print $data['rate_style']?>" value="3" type="button">
                <img width="20" src="http://projectbase.envigo.net/plugins/NewsVote/tpl/images/<?php print $data['stars3'] ?>" alt="rate 1" />
            </button>
            <button <?php !empty($data['btnExtra']) ? print $data['btnExtra'] : false ?> class="btnRate" title="rate 4" style="<?php print $data['rate_style']?>" value="4" type="button">
                <img width="20" src="http://projectbase.envigo.net/plugins/NewsVote/tpl/images/<?php print $data['stars4'] ?>" alt="rate 1" />
            </button>
            <button <?php !empty($data['btnExtra']) ? print $data['btnExtra'] : false ?> class="btnRate" title="rate 5" style="<?php print $data['rate_style']?>" value="5" type="button">
                <img width="20" src="http://projectbase.envigo.net/plugins/NewsVote/tpl/images/<?php print $data['stars5'] ?>" alt="rate 1" />
            </button>
        </form>
    </div>