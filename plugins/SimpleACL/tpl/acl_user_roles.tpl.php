<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<br/>
<?php isset($data['ADM_TABLE_TITLE']) ? print $data['ADM_TABLE_TITLE'] : false; ?>        
<?php isset($data['ACL_MSG']) ? print "<p> {$data['ACL_MSG']} </p>" : false; ?>
<form method="post" action="" id="user_search">
    <input type="text" name="username"/>
    <input type="submit" name="btnSearchUser" value="<?php print $LANGDATA['L_ACL_SEARCH'] ?>" />
</form>

<?php if (!empty($data['option_roles'])) { ?>
    <p><?php print $data['username'] ?></p>
    <form method='post' action='' id='form_user_roles'>
        <select class='option_roles' size='5' name='del_role_id'>
            <?php print $data['option_roles'] ?>
        </select>
        <input type="hidden" name="username" value='<?php print $data['username'] ?>' />
        <input type='submit' name='btnDeleteRole' value='<?php print $LANGDATA['L_ACL_DELETE'] ?>' />     
    </form>    
<?php } else if (!empty($data['username'])) { ?>
    <p><?php print $data['username'] ?></p>
    <p><?php print $LANGDATA['L_ACL_NO_ROLES_FOUND'] ?></p>
<?php } ?>
<?php if (!empty($data['roles'])) { ?>
    <form method='post' action='' id='form_add_roles'>
        <select class='add_role' name='add_role_id'>
            <?php print $data['roles'] ?>
        </select>
        <input type="hidden" name="username" value='<?php print $data['username'] ?>' />    
        <input type='submit' name='btnAddRole' value='<?php print $LANGDATA['L_ACL_ADD'] ?>' />     
    </form>
<?php } ?>
<br/>