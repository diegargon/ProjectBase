<?php
global $tpldata;
/* 
 *  Copyright @ 2016 Diego Garcia
 */
?>

<div  class="clear bodysize page">   
    <div class="profilebox">
        <form  action="" autocomplete="off" method="post"> 
            <h1>Profile page</h1> 
            <p>UserID: <?php print $_SESSION['uid']?></p>
            <p>Username: <?php print $_SESSION['username'] ?></p>
            <p>User SID: <?php print $_SESSION['sid'] ?></p>
        </form>
            
            
    </div>
</div>
