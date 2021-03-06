<?php

/*
 *  Copyright @ 2016 Diego Garcia
 * EN
 */
!defined('IN_WEB') ? exit : true;

$LNG['L_NEWS_WARN_NOLANG'] = "Warning: No version of this page in your language";
$LNG['L_NEWS_NOT_EXIST'] = "News not exists";
$LNG['L_CREATE_NEWS'] = "Create news";
$LNG['L_NEWS_TITLE'] = "Title <span class='text_small'> (Max/Min " . $cfg['NEWS_TITLE_MAX_LENGHT'] . "/" . $cfg['NEWS_TITLE_MIN_LENGHT'] . " characters)</span>";
$LNG['L_NEWS_LEAD'] = "Lead <span class='text_small'> (Max/Min " . $cfg['NEWS_LEAD_MAX_LENGHT'] . "/" . $cfg['NEWS_LEAD_MIN_LENGHT'] . " characters)</span>";
$LNG['L_NEWS_TEXT'] = "News text <span class='text_small'> (Max/Min " . $cfg['NEWS_TEXT_MAX_LENGHT'] . "/" . $cfg['NEWS_TEXT_MIN_LENGHT'] . " characters)</span>";
$LNG['L_NEWS_AUTHOR'] = "Author";
$LNG['L_NEWS_ANONYMOUS'] = "Anonymous";
$LNG['L_NEWS_LANG'] = "Language";
$LNG['L_NEWS_OTHER_OPTIONS'] = "Other options";
$LNG['L_NEWS_ERROR_INCORRECT_AUTHOR'] = "Username incorrect";
$LNG['L_NEWS_INTERNAL_ERROR'] = "Internal error, please disconnect and try again";
$LNG['L_NEWS_TITLE_ERROR'] = "There are some error in the title, check characters or provide a title if empty";
$LNG['L_NEWS_TITLE_MINMAX_ERROR'] = "The title must have between  " . $cfg['NEWS_TITLE_MAX_LENGHT'] . " and " . $cfg['NEWS_TITLE_MIN_LENGHT'] . " characteres";
$LNG['L_NEWS_LEAD_ERROR'] = "There are some error in the lead, check characters or provide a title if empty";
$LNG['L_NEWS_LEAD_MINMAX_ERROR'] = "The lead must have between " . $cfg['NEWS_LEAD_MAX_LENGHT'] . " and " . $cfg['NEWS_LEAD_MIN_LENGHT'] . " characteres";
$LNG['L_NEWS_TEXT_ERROR'] = "Empty text or characters not allowed";
$LNG['L_NEWS_TEXT_MINMAX_ERROR'] = "The news text must have between " . $cfg['NEWS_TEXT_MAX_LENGHT'] . " and " . $cfg['NEWS_TEXT_MIN_LENGHT'] . " characteres";
$LNG['L_NEWS_CATEGORY'] = "Category";
$LNG['L_NEWS_ADMIN'] = "Administrator";
$LNG['L_NEWS_ALL_NOADMIN'] = "All except admistration";
$LNG['L_NEWS_SUBMIT'] = "Can send news";
$LNG['L_NEWS_COMMENT'] = "Can comment news";
$LNG['L_NEWS_PAYMENT'] = "Pay group";
$LNG['L_NEWS_READ'] = "Can't read news";
$LNG['L_NEWS_SUBMITED_SUCCESSFUL'] = "News succesful submited";
$LNG['L_NEWS_FEATURED'] = "Feature";
$LNG['L_NEWS_MODERATION'] = "Moderation";
$LNG['L_NEWS_MODERATION_DESC'] = "Here you moderate the news sended to your web";
$LNG['L_NEWS_ERROR_WAITINGMOD'] = "News its wating moderation";
$LNG['L_NEWS_DELETE'] = "Delete";
$LNG['L_NEWS_EDIT'] = "Edit";
$LNG['L_NEWS_APPROVED'] = "Approve";
$LNG['L_NEWS_EDIT'] = "Edit";
$LNG['L_NEWS_DISABLE'] = "Disable";
$LNG['L_NEWS_CONFIRM_DEL'] = " Delete news, are you sure?";
$LNG['L_NEWS_EDIT_NEWS'] = "News edit";
$LNG['L_NEWS_UPDATE_SUCCESSFUL'] = "News update succesful";
$LNG['L_NEWS_NO_EDIT_PERMISS'] = "Can't edit, no rights";
$LNG['L_NEWS_FRONTPAGE'] = "Frontpage";
$LNG['L_NEWS_CATEGORY_DESC'] = "Submit or modify here categories for the news";
$LNG['L_NEWS_CREATE'] = "Create";
$LNG['L_NEWS_MODIFY'] = "Modify";
$LNG['L_NEWS_MODIFIED_CATS'] = "Modify categories";
$LNG['L_NEWS_CREATE_CAT'] = "Create categories";
$LNG['L_NEWS_CATEGORIES'] = "Categories";
$LNG['L_NEWS_INFRONTPAGE'] = "In Frontpage";
$LNG['L_NEWS_INFRONTPAGE_DESC'] = "Here you can see and change the news in your frontpage";
$LNG['L_NEWS_BACKPAGE'] = "Backpage";
$LNG['L_NEWS_SOURCE'] = "Source";
$LNG['L_NEWS_RELATED'] = "Related";
$LNG['L_NEWS_NEWLANG'] = "Translate";
$LNG['L_NEWS_TRANSLATOR'] = "Translator";
$LNG['L_NEWS_TRANSLATE_SUCCESSFUL'] = "News translated and submit successful";
$LNG['L_NEWS_TRANSLATE_BY'] = "Translate by ";
$LNG['L_NEWS_E_RELATED'] = "Incorrect/Not working related link, fix it or leave blank";
$LNG['L_NEWS_E_SOURCE'] = "Incorrect/Not working  source link, fix it or leave blank";
$LNG['L_NEWS_E_ALREADY_TRANSLATE_ALL'] = "News already translate to all active languages.";
$LNG['L_NEWS_NEW_PAGE'] = "New page";
$LNG['L_NEWS_CREATE_NEW_PAGE'] = "Create new page";
$LNG['L_NEWS_DELETE_NOEXISTS'] = "News deleted or inexistent";
$LNG['L_NEWS_EDITOR_BOLD'] = "<span class=\"bold\">B</span>";
$LNG['L_NEWS_EDITOR_ITALIC'] = "<span class=\"italic\">I</span>";
$LNG['L_NEWS_EDITOR_PARAGRAPH'] = "P";
$LNG['L_NEWS_EDITOR_UNDERLINE'] = "<span class=\"underline\">U</span>";
$LNG['L_NEWS_EDITOR_H2'] = "H2";
$LNG['L_NEWS_EDITOR_H3'] = "H3";
$LNG['L_NEWS_EDITOR_H4'] = "H4";
$LNG['L_NEWS_EDITOR_QUOTE'] = "Quote";
$LNG['L_NEWS_EDITOR_SIZE'] = "Size";
$LNG['L_NEWS_EDITOR_IMG'] = "Img";
$LNG['L_NEWS_EDITOR_URL'] = "URL";
$LNG['L_NEWS_EDITOR_STYLE'] = "Style";
$LNG['L_NEWS_EDITOR_DIVCLASS'] = "Class";
$LNG['L_NEWS_EDITOR_LIST'] = "List";
$LNG['L_NEWS_PREVIEW'] = "Preview";
$LNG['L_NEWS_HIDDE_PREVIEW'] = "Hide preview";
$LNG['L_NEWS_EDITOR_PRE'] = "Pre";
$LNG['L_NEWS_EDITOR_CODE'] = "Code";
$LNG['L_NEWS_SECTION'] = "section"; //Need mod htaccess if change
$LNG['L_NEWS_E_SEC_NOEXISTS'] = "Section not exists";
$LNG['L_NEWS_FATHER'] = "Father";
$LNG['L_NEWS_ORDER'] = "Weight";
