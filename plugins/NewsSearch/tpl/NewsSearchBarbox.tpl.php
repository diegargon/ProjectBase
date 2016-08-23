<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<li class="nav_right">
    <form id="search" action="<?php print $data['searchUrl'] ?>" method="post">
        <input id="searchTextInput" type="text" name="searchText" value="" />
    </form>
</li>