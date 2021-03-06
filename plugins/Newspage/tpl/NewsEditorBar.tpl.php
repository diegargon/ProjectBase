<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
?>
<div class="editorBar">
    <?= !empty($tpldata['NEWS_EDITOR_BAR_PRE']) ? $tpldata['NEWS_EDITOR_BAR_PRE'] : null ?>
    <button class="btnEditor" type="button" value="[b]$1[/b]"><?= $LNG['L_NEWS_EDITOR_BOLD'] ?></button>
    <button class="btnEditor" type="button" value="[i]$1[/i]"><?= $LNG['L_NEWS_EDITOR_ITALIC'] ?></button>
    <button class="btnEditor" type="button" value="[u]$1[/u]"><?= $LNG['L_NEWS_EDITOR_UNDERLINE'] ?></button>
    <button class="btnEditor" type="button" value="[p]$1[/p]"><?= $LNG['L_NEWS_EDITOR_PARAGRAPH'] ?></button>
    <button class="btnEditor" type="button" value="[h2]$1[/h2]"><?= $LNG['L_NEWS_EDITOR_H2'] ?></button>
    <button class="btnEditor" type="button" value="[h3]$1[/h3]"><?= $LNG['L_NEWS_EDITOR_H3'] ?></button>
    <button class="btnEditor" type="button" value="[h4]$1[/h4]"><?= $LNG['L_NEWS_EDITOR_H4'] ?></button>
    <button class="btnEditor" type="button" value="[pre]$1[/pre]"><?= $LNG['L_NEWS_EDITOR_PRE'] ?></button>
    <button class="btnEditor" type="button" value="[size=14]$1[/size]"><?= $LNG['L_NEWS_EDITOR_SIZE'] ?></button>
    <?php if ($cfg['NEWS_PARSER_ALLOW_IMG']) { ?>
        <button class="btnEditor" type="button" value="[img]$1[/img]"><?= $LNG['L_NEWS_EDITOR_IMG'] ?></button>
    <?php } ?>
    <?php if ($cfg['NEWS_PARSER_ALLOW_URL']) { ?>
        <button class="btnEditor" type="button" value="[url]$1[/url]"><?= $LNG['L_NEWS_EDITOR_URL'] ?></button>
    <?php } ?>
    <button class="btnEditor" type="button" value="[list]$1[/list]"><?= $LNG['L_NEWS_EDITOR_LIST'] ?></button>
    <button class="btnEditor" type="button" value="[style]$1[/style]"><?= $LNG['L_NEWS_EDITOR_STYLE'] ?></button>
    <button class="btnEditor" type="button" value="[blockquote]$1[/blockquote]"><?= $LNG['L_NEWS_EDITOR_QUOTE'] ?></button>
    <button class="btnEditor" type="button" value="[code]$1[/code]"><?= $LNG['L_NEWS_EDITOR_CODE'] ?></button>
    <button class="btnEditor" type="button" value="[div_class=?]$1[/div_class]"><?= $LNG['L_NEWS_EDITOR_DIVCLASS'] ?></button>
    <?= !empty($tpldata['NEWS_EDITOR_BAR_POST']) ? $tpldata['NEWS_EDITOR_BAR_POST'] : null ?>
</div>