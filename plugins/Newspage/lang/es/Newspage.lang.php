<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 * ES
 */
if (!defined('IN_WEB')) { exit; }

$LANGDATA['L_NEWS_WARN_NOLANG'] = "Aviso: No hay versión de esta pagina en su idioma";
$LANGDATA['L_NEWS_NOT_EXIST'] = "Noticia no existe";
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