<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function DebugWindow_init() {    
    print_debug("Debugwindow initiated", "PLUGIN_LOAD");
    
    getCSS_filePath("DebugWindow");
    register_action("add_to_footer", "get_dw_tpl","5");   
     //Lo siguiente no funciona por que añadir al iniciar no muestra todos los mensajes debug de despues la acción en cambio se realiza/recoge al final
     //al final de todo.
    //addto_tplvar("ADD_TO_FOOTER", getTPL_file("DebugWindow", null, $GLOBALS['debug']));
}

function get_dw_tpl() {
    return getTPL_file("DebugWindow", null, $GLOBALS['debug']);
}
