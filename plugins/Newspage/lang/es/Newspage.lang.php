<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 * ES
 */
if (!defined('IN_WEB')) { exit; }

$LANGDATA['L_NEWS_WARN_NOLANG'] = "Aviso: No hay versión de esta pagina en su idioma";
$LANGDATA['L_NEWS_NOT_EXIST'] = "Noticia borrada o no existe";
$LANGDATA['L_SEND_NEWS'] = "Enviar noticia";
$LANGDATA['L_NEWS_TITLE'] = "Titulo <span class='text_small'> (Max/Min ". $config['NEWS_TITLE_MAX_LENGHT'] ."/". $config['NEWS_TITLE_MIN_LENGHT'] ." caracteres)</span>";
$LANGDATA['L_NEWS_LEAD'] = "Entradilla <span class='text_small'> (Max/Min ". $config['NEWS_LEAD_MAX_LENGHT'] ."/". $config['NEWS_LEAD_MIN_LENGHT'] ." caracteres)</span>";
$LANGDATA['L_NEWS_TEXT'] = "Texto de la noticia <span class='text_small'> (Max/Min ". $config['NEWS_TEXT_MAX_LENGHT'] ."/". $config['NEWS_TEXT_MIN_LENGHT'] ." caracteres)</span>";
$LANGDATA['L_NEWS_MEDIA'] = "Foto o video principal de la noticia";
$LANGDATA['L_NEWS_AUTHOR'] = "Autor";
$LANGDATA['L_NEWS_ANONYMOUS'] = "Anonimo";
$LANGDATA['L_NEWS_LANG'] = "Idioma";
$LANGDATA['L_NEWS_OTHER_OPTIONS'] = "Otras opciones";
$LANGDATA['L_NEWS_ERROR_INCORRECT_AUTHOR'] = "Nombre de usuario incorrecto";
$LANGDATA['L_NEWS_INTERNAL_ERROR'] = "Error interno, por favor desconecte y vuelva a intentarlo";
$LANGDATA['L_NEWS_TITLE_ERROR'] = "Hay algun error en el titulo, compruebe que los caracteres sean correctos o que el campo no este vacio";
$LANGDATA['L_NEWS_TITLE_MINMAX_ERROR'] = "El titulo tiene que estar entre maximo de ". $config['NEWS_TITLE_MAX_LENGHT'] ." y un minimo de ". $config['NEWS_TITLE_MIN_LENGHT'] ." caracteres";
$LANGDATA['L_NEWS_LEAD_ERROR'] = "Hay algun error en la entradilla, compruebe que los caracteres sean correctos o que el campo no este vacio";
$LANGDATA['L_NEWS_LEAD_MINMAX_ERROR'] = "La entradilla tiene que estar entre maximo de ". $config['NEWS_LEAD_MAX_LENGHT'] ." y un minimo de ". $config['NEWS_LEAD_MIN_LENGHT'] ." caracteres";
$LANGDATA['L_NEWS_TEXT_ERROR'] = "Hay algun error en el texto de la noticia, compruebe que los caracteres sean correctos o que el campo no este vacio";
$LANGDATA['L_NEWS_TEXT_MINMAX_ERROR'] = "El texto de la noticia tiene que estar entre maximo de ". $config['NEWS_TEXT_MAX_LENGHT'] ." y un minimo de ". $config['NEWS_TEXT_MIN_LENGHT'] ." caracteres";
$LANGDATA['L_NEWS_CATEGORY'] = "Categoria";
$LANGDATA['L_NEWS_ADMIN'] = "Administrador";
$LANGDATA['L_NEWS_ALL_NOADMIN'] = "Todo excepto administración";
$LANGDATA['L_NEWS_SUBMIT'] = "Grupo que puede enviar";
$LANGDATA['L_NEWS_COMMENT'] = "Grupo que puede comentar";
$LANGDATA['L_NEWS_PAYMENT'] = "Grupo de pago";
$LANGDATA['L_NEWS_READ'] = "Grupo que puede leer noticias";
$LANGDATA['L_NEWS_SUBMITED_SUCESSFUL'] ="Noticia enviada con exito";
$LANGDATA['L_NEWS_FEATURED'] = "Destacada";
$LANGDATA['L_NEWS_MAIN_MEDIA'] = "Imagen o video principal";
$LANGDATA['L_NEWS_MEDIALINK_ERROR'] = "Imagen no valida";
$LANGDATA['L_NEWS_MODERATION'] = "Moderación";
$LANGDATA['L_NEWS_MODERATION_DESC'] = "Aquí podras moderar las noticias enviadas a su web";
$LANGDATA['L_NEWS_ERROR_WAITINGMOD'] = "La noticia esta en espera de moderación";
$LANGDATA['L_NEWS_DELETE'] = "Borrar";
$LANGDATA['L_NEWS_EDIT'] = "Editar";
$LANGDATA['L_NEWS_APPROVED'] = "Aprobar";
$LANGDATA['L_NEWS_EDIT'] = "Editar";
$LANGDATA['L_NEWS_DISABLE'] = "Deshabilitar";
$LANGDATA['L_NEWS_CONFIRM_DEL'] = "¿Estas seguro que quieres eliminar?";
$LANGDATA['L_NEWS_EDIT_NEWS'] = "Editar noticia";
$LANGDATA['L_NEWS_UPDATE_SUCESSFUL'] = "Noticia actualizada con exito";
$LANGDATA['L_NEWS_NO_EDIT_PERMISS'] = "No tienes permisos para editar";
$LANGDATA['L_NEWS_FRONTPAGE'] = "Portada";
$LANGDATA['L_NEWS_CATEGORY_DESC'] = "Crea y modica aquí las categorias para sus noticias";
$LANGDATA['L_NEWS_CREATE'] = "Crear";
$LANGDATA['L_NEWS_MODIFY'] = "Modificar";
$LANGDATA['L_NEWS_MODIFIED_CATS'] = "Modificar categorias";
$LANGDATA['L_NEWS_CREATE_CAT'] = "Crear categoria";
$LANGDATA['L_NEWS_CATEGORIES'] = "Categorias";
$LANGDATA['L_NEWS_INFRONTPAGE'] = "En portada";
$LANGDATA['L_NEWS_INFRONTPAGE_DESC'] = "Aquí podras ver y cambiar la lista de noticias en portada";
$LANGDATA['L_NEWS_BACKPAGE'] = "Contraportada";
$LANGDATA['L_NEWS_SOURCE'] = "Fuente";
$LANGDATA['L_NEWS_RELATED'] = "Relacionadas";
$LANGDATA['L_NEWS_NEWLANG'] = "Nuevo lenguaje";
$LANGDATA['L_NEWS_TRANSLATOR'] = "Traductor";
$LANGDATA['L_NEWS_TRANSLATE_SUCESSFUL'] = "Noticia traducida enviada con exito";
$LANGDATA['L_NEWS_TRANSLATE_BY'] = "Traducido por ";
$LANGDATA['L_NEWS_E_RELATED'] = "Enlace relacionado incorrecto o no funciona, corrijalo o dejelo en blanco";
$LANGDATA['L_NEWS_E_SOURCE'] = "Enlace fuente incorrecto o no funciona, corrijalo o dejelo en blanco";
$LANGDATA['L_NEWS_E_ALREADY_TRANSLATE_ALL'] = "La noticia ya esta traducida a todos los idiomas activos.";