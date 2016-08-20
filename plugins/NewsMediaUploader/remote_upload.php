<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

global $config, $db, $sm;

includePluginFiles("NewsMediaUploader");

if ( (!$user = $sm->getSessionUser())  ) {
    if ($config['NMU_ALLOW_ANON']) {
        $user['uid'] = 0;
    } else {
        die('{"status": "1", "msg": "'. $LANGDATA['L_NMU_W_DISABLE'] .'"}');
        exit();
    }   
}
$url = S_POST_URL("url", null, null, 1);

if (!empty($url) ) {
    $url = S_VALIDATE_MEDIA($url, null, null, 1); //force no remote check, we check here
}

if (empty($url) || $url == -1 || ! $headers = get_headers( $url , 1) ) {    
        die('{"status": "2", "msg": "'.  $LANGDATA['L_NMU_E_URL'] .'"}');
        exit();
}
//print_r($headers);
if(!empty($headers['Content-Lenght'])) { //Nginx not send content-lenght not allow if wrong? check size after downlad?
    $content_lenght = $headers['Content-Length']; 
    $max_bytes = NMU_convertToBytes($config['NMU_MAX_FILESIZE']);
    if ($content_lenght > $max_bytes) {
        die('{"status": "3", "msg": "'.  $LANGDATA['L_NMU_E_TOOBIG'] .'"}');
        exit();
    }
}
if(empty($headers['Content-Type'])) {
        die('{"status": "4", "msg": "'.  $LANGDATA['L_NMU_E_URL'] .'"}');
        exit();    
}

$content_type = explode("/", $headers['Content-Type']);
$image_type = $content_type[1];
$content_type = $content_type[0];

if ($content_type != "image") {
        die('{"status": "5", "msg": "'.  $LANGDATA['L_NMU_E_NOMEDIA'] .'"}');
        exit();        
}

$ssl_off=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
); 

$image = file_get_contents($url, false, stream_context_create ($ssl_off) );

$fileName = uniqid("file_") . "." . $image_type;
$filePath = $config['NMU_UPLOAD_DIR'] . DIRECTORY_SEPARATOR . $fileName ;
if(file_exists($filePath)) {
        die('{"status": "6", "msg": "'.  $LANGDATA['L_NMU_E_ALREADY_EXISTS'] .'"}');
        exit();        
}

file_put_contents($filePath, $image);
// insert DB and make thumbs
$filePathSelector = $config['NMU_UPLOAD_DIR'] . "[S]" . $fileName;

$insert_ary = array(
    "plugin" => "news_img_upload",
    "source_id" => $user['uid'],
    "type" => "image",
    "link" => $filePathSelector,
);
$db->insert("links", $insert_ary);

$thumbsDir = $config['NMU_UPLOAD_DIR'] . DIRECTORY_SEPARATOR . "thumbs";
$mobileDir = $config['NMU_UPLOAD_DIR'] . DIRECTORY_SEPARATOR . "mobile";
$desktopDir = $config['NMU_UPLOAD_DIR'] . DIRECTORY_SEPARATOR . "desktop";


if( (getLib("ImageLib", "0.1")) && ( $config['NMU_CREATE_IMG_THUMBS'] || $config['NMU_CREATE_IMG_MOBILE'] ) ) {
    $imglib = new ImageLib;
    if ($config['NMU_CREATE_IMG_THUMBS']) {
        $thumb_filePath =  $thumbsDir . DIRECTORY_SEPARATOR . $fileName; 
        $imglib->do_thumb($filePath, $thumb_filePath, $config['NMU_THUMBS_WIDTH'] );
    }  
    if ( $config['NMU_CREATE_IMG_MOBILE'] ) {
        $mobile_filePath =  $mobileDir . DIRECTORY_SEPARATOR . $fileName;
        $imglib->do_thumb($filePath, $mobile_filePath, $config['NMU_MOBILE_WIDTH'] );
    }
    if ( $config['NMU_CREATE_IMG_DESKTOP'] ) {
        $desktop_filePath =  $desktopDir . DIRECTORY_SEPARATOR . $fileName;
        $imglib->do_thumb($filePath, $desktop_filePath, $config['NMU_DESKTOP_WIDTH'] );
    }
}

die('{"status": "ok", "msg": "'. $LANGDATA['L_NMU_UPLOAD_SUCCESS'] .'", "filename": "'. $filePathSelector .'"}');