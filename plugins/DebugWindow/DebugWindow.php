<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function DebugWindow_init() { 
    global $tpl;
    print_debug("Debugwindow initiated", "PLUGIN_LOAD");
    
    $tpl->getCSS_filePath("DebugWindow");
    register_action("add_to_footer", "get_dw_tpl","5");   
     //Lo siguiente no funciona por que añadir al iniciar no muestra todos los mensajes debug de despues la acción en cambio se realiza/recoge al final
     //al final de todo.
    //$tpl->addto_tplvar("ADD_TO_FOOTER", $tpl->getTPL_file("DebugWindow", null, $GLOBALS['debug']));
}

function get_dw_tpl() {
    global $tpl, $debug;
    return $tpl->getTPL_file("DebugWindow", null, $debug);
}
