<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
?>
<div class="editorBar">
    <?php !empty($tpldata['NEWS_EDITOR_BAR_PRE']) ? print $tpldata['NEWS_EDITOR_BAR_PRE'] : false ?>
    <button class="btnEditor" type="button" value="[b]$1[/b]"><?php print $LANGDATA['L_NEWS_EDITOR_BOLD'] ?></button>
    <button class="btnEditor" type="button" value="[i]$1[/i]"><?php print $LANGDATA['L_NEWS_EDITOR_ITALIC'] ?></button>
    <button class="btnEditor" type="button" value="[u]$1[/u]"><?php print $LANGDATA['L_NEWS_EDITOR_UNDERLINE'] ?></button>
    <button class="btnEditor" type="button" value="[p]$1[/p]"><?php print $LANGDATA['L_NEWS_EDITOR_PARAGRAPH'] ?></button>
    <button class="btnEditor" type="button" value="[h2]$1[/h2]"><?php print $LANGDATA['L_NEWS_EDITOR_H2'] ?></button>
    <button class="btnEditor" type="button" value="[h3]$1[/h3]"><?php print $LANGDATA['L_NEWS_EDITOR_H3'] ?></button>
    <button class="btnEditor" type="button" value="[h4]$1[/h4]"><?php print $LANGDATA['L_NEWS_EDITOR_H4'] ?></button>
    <button class="btnEditor" type="button" value="[pre]$1[/pre]"><?php print $LANGDATA['L_NEWS_EDITOR_PRE'] ?></button>
    <button class="btnEditor" type="button" value="[size=14]$1[/size]"><?php print $LANGDATA['L_NEWS_EDITOR_SIZE'] ?></button>
    <?php if ($config['NEWS_PARSER_ALLOW_IMG']) { ?>
        <button class="btnEditor" type="button" value="[img]$1[/img]"><?php print $LANGDATA['L_NEWS_EDITOR_IMG'] ?></button>
    <?php } ?>
    <?php if ($config['NEWS_PARSER_ALLOW_URL']) { ?>
        <button class="btnEditor" type="button" value="[url]$1[/url]"><?php print $LANGDATA['L_NEWS_EDITOR_URL'] ?></button>
    <?php } ?>
    <button class="btnEditor" type="button" value="[list]$1[/list]"><?php print $LANGDATA['L_NEWS_EDITOR_LIST'] ?></button>
    <button class="btnEditor" type="button" value="[style]$1[/style]"><?php print $LANGDATA['L_NEWS_EDITOR_STYLE'] ?></button>
    <button class="btnEditor" type="button" value="[blockquote]$1[/blockquote]"><?php print $LANGDATA['L_NEWS_EDITOR_QUOTE'] ?></button>
    <button class="btnEditor" type="button" value="[code]$1[/code]"><?php print $LANGDATA['L_NEWS_EDITOR_CODE'] ?></button>
    <button class="btnEditor" type="button" value="[div_class=?]$1[/div_class]"><?php print $LANGDATA['L_NEWS_EDITOR_DIVCLASS'] ?></button>
    <?php !empty($tpldata['NEWS_EDITOR_BAR_POST']) ? print $tpldata['NEWS_EDITOR_BAR_POST'] : false ?>
</div>