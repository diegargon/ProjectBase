<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */

function robots_check() {
    global $url_skip;
    return 0;
}

function GetPage ($url) {
    global $cfg;
    
    $ch = curl_init ($url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_USERAGENT, $cfg['USER_AGENT']);

    $data = curl_exec($ch);

    curl_close($ch);

    return $data;
}

function Scan($url) {
    global $url_scanned, $url_list, $url_skip_regx;
    global $cfg;
    
    if( array_search($url, $url_scanned) !== false ) {
        return false;
    }
    $url_scanned[] = $url;

    $headers = get_headers ($url, 1);
    //var_dump($headers);
    
    if (strpos ($headers[0], "404") !== false) {
        echo "Not found: $url" . NL;
        return false;
    }        
    if ( (strpos ($headers[0], "301") !== false) ||(strpos ($headers[0], "302") !== false)) {   
        if(is_array($url)) {
            echo "Redirected 30X to array: $url[1]" . NL;
            $url = $url[1];
        } else {
            //$url = $cfg['WEB_URL'] . substr ($url, +1);
            $url = $headers["Location"];     // Continue with new URL
            echo "Redirected 30X to: $url" . NL;
        }
    } else if (strpos ($headers[0], "200") == false) {
        $url = $headers["Location"];
        echo "Skip HTTP code $headers[0]: $url" . NL;
        return false;
    }
    
    if (is_array ($headers["Content-Type"])) {
        $content = explode (";", $headers["Content-Type"][0]);
    } else {
        $content = explode (";", $headers["Content-Type"]);
    }
    
    $content_type = trim (strtolower ($content[0]));
    
    if ($content_type != "text/html") {  
        return false;
    }  
    
    $html = GetPage($url);
    
    $links = GetLinks($html);
    FixLinks($links);
    
    foreach ($url_list as $value ) {
        echo "Scanning... $value\n";
        Scan($value);
    }
    //foreach ($links as $key => $value) { echo " '$key'-> '$value' \n"; }
    //foreach ($url_list as $key => $value) { echo " '$key'-> '$value' \n"; }
}
            

function GetLinks($html) {
    $matches = []; 
    //$href_regex = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";  
    $href_regex = "<a\s[^>]*href=([\"\']??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
    
    preg_match_all("/$href_regex/siU", $html, $matches);
    return $matches[2];           
}

function FixLinks($links) {
    global $cfg, $url_list, $url_skip_regx;
       
    foreach($links as $key => $value ) {
        //echo "Processing ... '$value' \n";
        $value = trim($value);
        
        if ( empty($value) || (strpos($value, "javascript") !== false) || (strpos($value, "#") !== false)  ) {
            unset($links[$key]);
           // echo "Unsetting 1...\n";
        } else if(  strpos($value, "http") === false ) {
            if (strpos($value, "/", 0) !== false ) {
              //  echo "Antes +$value+\n";
                $value = substr ($value, +1);
              //  echo "Despues +$value+\n";
            }
            if(!empty($value)) {
                $links[$key] = $cfg['WEB_URL'] . $value;
            } else {
                unset($links[$key]);
              //  echo "Unsetting 2...\n";
            }
        } else {
           // echo "Check host...\n";
            $host = parse_url($value, PHP_URL_HOST);
            if ($host != $cfg['HOST']) {
              //  echo "Dominio saliente skipping... $value \n";
                unset($links[$key]);
            } else {
             //   echo "Nada raro continuamos con $value \n";
            }
        }
    }
    
    foreach ($url_skip_regx as $reg_val) {
        foreach ($links as $key => $link) {
            if(preg_match($reg_val, $link)) {
                echo "Discarding by Skip REGX $link \n";
                unset($links[$key]);
            }
        }
    }
    foreach($links as $key => $value ) {
        if( array_search($links[$key], $url_list) === false ) {
            $url_list[] = $links[$key];
        }
    }

}

function build_sitemap() {
    global $url_list, $cfg;
    
    echo "Creating Sitemap:" . NL;
    
    $content = "<?xml version=\"1.0\" encoding=\"{$cfg['CHARSET']}\"?>\n"
        . "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n"    
        . "<url>\n"
        . "<loc>" . $cfg['WEB_URL'] . "</loc>\n";
       
        $content .= "<changefreq>" . $cfg['DFL_FRQ'] . "</changefreq>\n"
        . "<priority>" . $cfg['DFL_PRIO']  . "</priority>\n"
        . "</url>\n";
     

  
    foreach ($url_list as $link) { 
        echo ".";
        
        
        $url = htmlspecialchars($link, ENT_XML1, $cfg['CHARSET'] ) ;
        if ($cfg['CHARSET'] == 'UTF-8') {
            $url = utf8_encode($url);
        }
        $content .= "<url>\n"
        . "<loc>" . $url . "</loc>\n";
        $content .= "<changefreq>" . $cfg['DFL_FRQ'] . "</changefreq>\n";
        $content .= "<priority>" . $cfg['DFL_PRIO']  . "</priority>\n"
        . "</url>\n";
    }
    $content .= "</urlset>";
    
    echo "Writing to file..." . NL;
    $fp = fopen($cfg["SITEMAP_FILE"], "w");
    if (!$fp) {
        echo "Cannot create {$cfg["SITEMAP_FILE"]}" . NL;
        return false;
    }  
    fwrite ($fp, $content);
    fclose($fp);    
    echo "Done...";
}