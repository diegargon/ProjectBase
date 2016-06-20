<?php
if (!defined('IN_WEB')) { exit; }
?>
<script>
function toggleMenu() {
    document.getElementsByClassName("main-nav")[0].classList.toggle("responsive");
}
</script>
<body id="Top">
<div class="main_container">
<div class="container">    
<div class="row1">        
        <header id="header" class="clear">
        <?php isset($tpldata['ADD_HEADER_BEGIN']) ? print $tpldata['ADD_HEADER_BEGIN'] : false ?>            
        <div id="logo">
            <h1><a href="/<?php if ($config['FRIENDLY_URL']) {
                    echo $config['WEB_LANG'];
                } else {
                    echo "?lang=". $config['WEB_LANG'];
                }
                ?>
                "><?php echo $config['TITLE']?></a></h1>
            <h2><?php echo $config['WEB_DESC']?></h2>
        </div>
        <?php 
        if($config['NAV_MENU']) { ?>
        <nav>
            <ul class="main-nav">              
                <li class="nav_right resp-icon"><a rel="nofollow" href="javascript:void(0);" onclick="toggleMenu()">&#9776;</a></li>                
                <?php 
                if ($config['NAV_MENU_HOME']) { 
                  echo "<li class='nav_left lihome'>";
                  echo "<a href='/";
                  if ($config['FRIENDLY_URL']) {
                    echo "{$config['WEB_LANG']}";
                  } else {
                    echo "?lang={$config['WEB_LANG']}";  
                  }
                  echo "'><img src='/plugins/tplBasic/images/home.png' alt='{$LANGDATA['L_HOME']}' />";                        
                  echo "</a></li>";           
                }
                  isset($tpldata['NAV_ELEMENT']) ? print $tpldata['NAV_ELEMENT'] : false;
                ?>

            </ul>
        </nav>            
        <?php } ?>
        <?php isset($tpldata['ADD_HEADER_END']) ? print $tpldata['ADD_HEADER_END']:false ?>
        </header>
</div>    
    
    <?php 
    isset($tpldata['PRE_ACTION_ADD_TO_BODY']) ? print $tpldata['PRE_ACTION_ADD_TO_BODY'] : false;
    
    if (!isset($tpldata['ADD_TO_BODY'])) {
        echo "<p>Hello World</p>";
    } else {
       print $tpldata['ADD_TO_BODY'];
    }
    isset($tpldata['POST_ACTION_ADD_TO_BODY']) ? print $tpldata['POST_ACTION_ADD_TO_BODY'] : false;



