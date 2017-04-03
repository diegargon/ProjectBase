<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<?php if ($data['ROW_CTR'] == 1) { ?>
    <section><h3><?php print $LANGDATA['L_ML_MODIFY_LANGS'] ?></h3>
    <?php } ?>
    <form id='form_modify' action='#' method='post'>
        <label><?php print $LANGDATA['L_ML_NAME'] ?>: </label>
        <input maxlength='32' type='text' name='lang_name' id='lang_name' value='<?php print $data['lang_name'] ?>' />
        <label><?php print $LANGDATA['L_ML_ACTIVE'] ?>: </label>
        <?php if ($data['active']) { ?>
            <input checked type='checkbox' name='active' id='active'  value='1' />
        <?php } else { ?>
            <input type='checkbox' name='active' value='1'/>
        <?php } ?>        
        <label><?php print $LANGDATA['L_ML_ISOCODE'] ?>: </label>
        <input maxlength='2' type='text' name='iso_code' id='iso_code' value='<?php print $data['iso_code'] ?>' />
        <input type='hidden' name='lang_id' value='<?php print $data['lang_id'] ?>' />
        <input type='submit' id='btnModifyLang' name='btnModifyLang' value='<?php print $LANGDATA['L_ML_MODIFY'] ?>' />
        <input type='submit' id='btnDeleteLang' name='btnDeleteLang' value='<?php print $LANGDATA['L_ML_DELETE'] ?>' onclick="return confirm('<?php print $LANGDATA['L_ML_SURE'] ?>')" />
    </form>
    <?php if ($data['ROW_CTR'] == 0) { ?>    
    </section>
<?php }