<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
class bbcode {
    private $bbcode = array(
        '~\[p\](.*?)\[/p\]~si'                                              => '<p>$1</p>',
        '~\[b\](.*?)\[/b\]~si'                                              => '<b>$1</b>',
	'~\[i\](.*?)\[/i\]~si'                                              => '<i>$1</i>',
	'~\[u\](.*?)\[/u\]~si'                                              => '<span style="text-decoration:underline;">$1</span>',
	'~\[quote\](.*?)\[/quote\]~si'                                      => '<pre>$1</'.'pre>',
	'~\[size=(.*?)\](.*?)\[/size\]~si'                                  => '<span style="font-size:$1px;">$2</span>',  //check size=\\d+\\ for numbers only o size=[0-9]+
	'~\[color=((?:[a-zA-Z]|#[a-fA-F0-9]{3,6})+)\](.*?)\[/color\]~si'    => '<span style="color:$1;">$2</span>',
	'~\[url\]((?:ftp|https?)://.*?)\[/url\]~si'                         => '<a href="$1">$1</a>',
	'~\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~si'       => '<img class="link_image" src="$1" alt="" />',
        '~\[img w=(.*?)](.*?)\[\/img\]~si'                                  => '<img class="link_image" width="$1" src="$2" alt="" />',
        '~\[list\](.*?)\\[\\/list\\]~si'                                    => '<ol>$1</ol>',
        '~\[\*\](.+)\[\/\*\]~i'                                             => '<li>$1</li>',
//        '~\[style=(.*?)\](.*?)\[/style\]~si'                                => '<div style="$1">$2</div>',
        '~\[style=(.*?)\]~si'                                               => '<div style="$1">',
        '~\[/style\]~si'                                                    => '</div>',
        '~\[h1\](.*?)\[/h1\]~si'                                            => '<h1>$1</h1>',
        '~\[h2\](.*?)\[/h2\]~si'                                            => '<h2>$1</h2>',
        '~\[h3\](.*?)\[/h3\]~si'                                            => '<h3>$1</h3>',
    );

    function parse($text) {
        $text =  preg_replace(array_keys($this->bbcode), array_values($this->bbcode), $text);
        return $text; //str_replace('\r\n', PHP_EOL, $text);
    }
}
