<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<li class="nav_right">
    <form id="search" action="<?php print $data['searchUrl'] ?>" method="get">
        <input id="searchTextInput" type="text" name="q" value="" />
    </form>
</li>