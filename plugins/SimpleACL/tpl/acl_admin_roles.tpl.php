<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<br/>
<?php isset($data['ADM_TABLE_TITLE'])? print $data['ADM_TABLE_TITLE'] : false; ?>        
<?php isset($data['ACL_MSG']) ? print "<p> {$data['ACL_MSG']} </p>" : false; ?>

<form method="post" action="" id="roles_mng">  
<table class='acl_table'>
    <tr>
        <?php isset($data['ADM_TABLE_TH'])? print $data['ADM_TABLE_TH'] : false; ?>        
    </tr>
    <?php isset($data['ADM_TABLE_ROW']) ? print $data['ADM_TABLE_ROW'] :false; ?> 
</table>
</form>
<form method="post" action="" id="new_role">  
<table class="acl_table"> 
    <tr>
        <?php isset($data['ADM_TABLE_TH'])? print $data['ADM_TABLE_TH'] : false; ?>        
    </tr>    
    <tr>        
        <td><input name="r_level" type="text" maxlength="2" size="1" required /></td>
        <td><input name="r_group" type="text" maxlength="18" size="11" required /></td>
        <td><input name="r_type" type="text" maxlength="14" size="11" required /></td>
        <td><input name="r_name" type="text" maxlength="32" size="22" required /></td>
        <td><input name="r_description" type="text" maxlength="255" size="22" /></td>
        <td><input name="btnNewRole" type="submit" value='<?php print $LANGDATA['L_ACL_SEND']?>' /></td>  
    </tr>
</table>    
</form>
<br/>