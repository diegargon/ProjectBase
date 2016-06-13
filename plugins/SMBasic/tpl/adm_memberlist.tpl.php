<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<br/>
<?php isset($data['ADM_TABLE_TITLE'])? print $data['ADM_TABLE_TITLE'] : false; ?>        
<table>
    <tr>
        <?php isset($data['ADM_TABLE_TH'])? print $data['ADM_TABLE_TH'] : false; ?>        
    </tr>
    <?php isset($data['ADM_TABLE_ROW']) ? print $data['ADM_TABLE_ROW'] :false; ?>
    
</table>
<br/>