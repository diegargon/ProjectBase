<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function UserExtra_init() {
    global $UXtra;
    print_debug("UserExtra initiated", "PLUGIN_LOAD");

    includePluginFiles("UserExtra");

    !isset($UXtra) ? $UXtra = new UserExtra : false;
}
