<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

if (!empty($data['TPL_FIRST'])) {
    ?>
    <div  class="clear bodysize page">
        <div id="searchResult">
            <section>
                <table class="searchTable">
                    <tr>
                        <td ><h2><?php print $LANGDATA['L_NS_SEARCH_RESULT'] ?></h2></td>
                    </tr>
<?php } ?>
                <tr>
                    <td>
                        <a href="<?php print $data['url'] ?>">
                            <div class="s_news_title"><?php print $data['title'] ?></div>
                            <div class="s_news_lead"><?php print $data['lead'] ?></div>
                        </a>
                    </td>
                </tr>
<?php if (!empty($data['TPL_LAST'])) { ?>
                </table>
            </section>
        </div>
    </div>
    <?php
}
