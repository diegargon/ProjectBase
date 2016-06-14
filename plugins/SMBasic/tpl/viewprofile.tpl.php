<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<div  class="clear bodysize page">   
    <div class="profile_box">        
        <h1><?php print $LANGDATA['L_VIEWPROFILE']?></h1> 
        <dl>
            <dt><span><?php print $LANGDATA['L_USERNAME']?> </span></dt>                           
            <dd><span><?php print $data['username'] ?> </span></dd>
        </dl>
        <dl>
            <dt><span><?php print $LANGDATA['L_EMAIL']?> </span></dt>
            <dd><span><?php print $data['email'] ?> </span></dd>
        </dl>
        <dl>
            <dt><span><?php print $LANGDATA['L_SM_REGISTERED']?> </span></dt>
            <dd><span><?php print format_date($data['regdate']) ?> </span></dd>
        </dl>        
        <dl>
            <dt><span><?php print $LANGDATA['L_SM_LASTLOGIN']?> </span></dt>
            <dd><span><?php print format_date($data['last_login']) ?> </span></dd>
        </dl>        
        <p class='p_center_medium'><a href="<?php print $config['BACKLINK']?>"><?php print $LANGDATA['L_BACK'] ?></a></p>
    </div>
</div>