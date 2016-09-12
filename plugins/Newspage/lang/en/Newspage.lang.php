<?php

/*
 *  Copyright @ 2016 Diego Garcia
 * EN
 */
!defined('IN_WEB') ? exit : true;

$LANGDATA['L_NEWS_WARN_NOLANG'] = "Warning: No version of this page in your language";
$LANGDATA['L_NEWS_NOT_EXIST'] = "News not exists";
$LANGDATA['L_SEND_NEWS'] = "Send";
$LANGDATA['L_NEWS_TITLE'] = "Title <span class='text_small'> (Max/Min " . $config['NEWS_TITLE_MAX_LENGHT'] . "/" . $config['NEWS_TITLE_MIN_LENGHT'] . " characters)</span>";
$LANGDATA['L_NEWS_LEAD'] = "Lead <span class='text_small'> (Max/Min " . $config['NEWS_LEAD_MAX_LENGHT'] . "/" . $config['NEWS_LEAD_MIN_LENGHT'] . " characters)</span>";
$LANGDATA['L_NEWS_TEXT'] = "News text <span class='text_small'> (Max/Min " . $config['NEWS_TEXT_MAX_LENGHT'] . "/" . $config['NEWS_TEXT_MIN_LENGHT'] . " characters)</span>";
$LANGDATA['L_NEWS_AUTHOR'] = "Author";
$LANGDATA['L_NEWS_ANONYMOUS'] = "Anonymous";
$LANGDATA['L_NEWS_LANG'] = "Language";
$LANGDATA['L_NEWS_OTHER_OPTIONS'] = "Other options";
$LANGDATA['L_NEWS_ERROR_INCORRECT_AUTHOR'] = "Username incorrect";
$LANGDATA['L_NEWS_INTERNAL_ERROR'] = "Internal error, please disconnect and try again";
$LANGDATA['L_NEWS_TITLE_ERROR'] = "There are some error in the title, check characters or provide a title if empty";
$LANGDATA['L_NEWS_TITLE_MINMAX_ERROR'] = "The title must have between  " . $config['NEWS_TITLE_MAX_LENGHT'] . " and " . $config['NEWS_TITLE_MIN_LENGHT'] . " characteres";
$LANGDATA['L_NEWS_LEAD_ERROR'] = "There are some error in the lead, check characters or provide a title if empty";
$LANGDATA['L_NEWS_LEAD_MINMAX_ERROR'] = "The lead must have between " . $config['NEWS_LEAD_MAX_LENGHT'] . " and " . $config['NEWS_LEAD_MIN_LENGHT'] . " characteres";
$LANGDATA['L_NEWS_TEXT_ERROR'] = "Empty text or characters not allowed";
$LANGDATA['L_NEWS_TEXT_MINMAX_ERROR'] = "The news text must have between " . $config['NEWS_TEXT_MAX_LENGHT'] . " and " . $config['NEWS_TEXT_MIN_LENGHT'] . " characteres";
$LANGDATA['L_NEWS_CATEGORY'] = "Category";
$LANGDATA['L_NEWS_ADMIN'] = "Administrator";
$LANGDATA['L_NEWS_ALL_NOADMIN'] = "All except admistration";
$LANGDATA['L_NEWS_SUBMIT'] = "Can send news";
$LANGDATA['L_NEWS_COMMENT'] = "Can comment news";
$LANGDATA['L_NEWS_PAYMENT'] = "Pay group";
$LANGDATA['L_NEWS_READ'] = "Can't read news";
$LANGDATA['L_NEWS_SUBMITED_SUCESSFUL'] = "News succesful submited";
$LANGDATA['L_NEWS_FEATURED'] = "Feature";
$LANGDATA['L_NEWS_MODERATION'] = "Moderation";
$LANGDATA['L_NEWS_MODERATION_DESC'] = "Here you moderate the news sended to your web";
$LANGDATA['L_NEWS_ERROR_WAITINGMOD'] = "News its wating moderation";
$LANGDATA['L_NEWS_DELETE'] = "Delete";
$LANGDATA['L_NEWS_EDIT'] = "Edit";
$LANGDATA['L_NEWS_APPROVED'] = "Approve";
$LANGDATA['L_NEWS_EDIT'] = "Edit";
$LANGDATA['L_NEWS_DISABLE'] = "Disable";
$LANGDATA['L_NEWS_CONFIRM_DEL'] = " Delete news, are you sure?";
$LANGDATA['L_NEWS_EDIT_NEWS'] = "News edit";
$LANGDATA['L_NEWS_UPDATE_SUCESSFUL'] = "News update succesful";
$LANGDATA['L_NEWS_NO_EDIT_PERMISS'] = "Can't edit, no rights";
$LANGDATA['L_NEWS_FRONTPAGE'] = "Frontpage";
$LANGDATA['L_NEWS_CATEGORY_DESC'] = "Submit or modify here categories for the news";
$LANGDATA['L_NEWS_CREATE'] = "Create";
$LANGDATA['L_NEWS_MODIFY'] = "Modify";
$LANGDATA['L_NEWS_MODIFIED_CATS'] = "Modify categories";
$LANGDATA['L_NEWS_CREATE_CAT'] = "Create categories";
$LANGDATA['L_NEWS_CATEGORIES'] = "Categories";
$LANGDATA['L_NEWS_INFRONTPAGE'] = "In Frontpage";
$LANGDATA['L_NEWS_INFRONTPAGE_DESC'] = "Here you can see and change the news in your frontpage";
$LANGDATA['L_NEWS_BACKPAGE'] = "Backpage";
$LANGDATA['L_NEWS_SOURCE'] = "Source";
$LANGDATA['L_NEWS_RELATED'] = "Related";
$LANGDATA['L_NEWS_NEWLANG'] = "Translate";
$LANGDATA['L_NEWS_TRANSLATOR'] = "Translator";
$LANGDATA['L_NEWS_TRANSLATE_SUCESSFUL'] = "News translated and submit sucessful";
$LANGDATA['L_NEWS_TRANSLATE_BY'] = "Translate by ";
$LANGDATA['L_NEWS_E_RELATED'] = "Incorrect/Not working related link, fix it or leave blank";
$LANGDATA['L_NEWS_E_SOURCE'] = "Incorrect/Not working  source link, fix it or leave blank";
$LANGDATA['L_NEWS_E_ALREADY_TRANSLATE_ALL'] = "News already translate to all active languages.";
$LANGDATA['L_NEWS_NEW_PAGE'] = "New page";
$LANGDATA['L_NEWS_CREATE_NEW_PAGE'] = "Create new page";
$LANGDATA['L_NEWS_DELETE_NOEXISTS'] = "News deleted or inexistent";
$LANGDATA['L_NEWS_EDITOR_BOLD'] = "<b>B</b>";
$LANGDATA['L_NEWS_EDITOR_ITALIC'] = "<i>I</i>";
$LANGDATA['L_NEWS_EDITOR_PARAGRAPH'] = "P";
$LANGDATA['L_NEWS_EDITOR_UNDERLINE'] = "<span style='text-decoration:underline;'>U</span>";
$LANGDATA['L_NEWS_EDITOR_H2'] = "H2";
$LANGDATA['L_NEWS_EDITOR_H3'] = "H3";
$LANGDATA['L_NEWS_EDITOR_H4'] = "H4";
$LANGDATA['L_NEWS_EDITOR_QUOTE'] = "Quote";
$LANGDATA['L_NEWS_EDITOR_SIZE'] = "Size";
$LANGDATA['L_NEWS_EDITOR_IMG'] = "Img";
$LANGDATA['L_NEWS_EDITOR_URL'] = "URL";
$LANGDATA['L_NEWS_EDITOR_STYLE'] = "Style";
$LANGDATA['L_NEWS_EDITOR_DIVCLASS'] = "Class";
$LANGDATA['L_NEWS_EDITOR_LIST'] = "List";
$LANGDATA['L_NEWS_PREVIEW'] = "Preview";
$LANGDATA['L_NEWS_HIDDE_PREVIEW'] = "Hide preview";
$LANGDATA['L_NEWS_EDITOR_PRE'] = "Pre";
$LANGDATA['L_NEWS_EDITOR_CODE'] = "Code";
$LANGDATA['L_NEWS_SECTION'] = "section"; //Need mod htaccess if change
$LANGDATA['L_NEWS_E_SEC_NOEXISTS'] = "Section not exists";
$LANGDATA['L_NEWS_FATHER'] = "Father";
$LANGDATA['L_NEWS_ORDER'] = "Weight";
