<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
class parse_text {
    private $bbcode = array(
        '~\[p\](.*?)\[/p\]~si'                                              => '<p>$1</p>',
        '~\[b\](.*?)\[/b\]~si'                                              => '<b>$1</b>',
	'~\[i\](.*?)\[/i\]~si'                                              => '<i>$1</i>',
	'~\[u\](.*?)\[/u\]~si'                                              => '<span style="text-decoration:underline;">$1</span>',
	'~\[pre\](.*?)\[/pre\]~si'                                          => '<pre>$1</pre>',
	'~\[size=((?:[1-9][0-9]?[0-9]?))\](.*?)\[/size\]~si'                => '<span style="font-size:$1px;">$2</span>',
	'~\[color=((?:[a-zA-Z]|#[a-fA-F0-9]{3,6})+)\](.*?)\[/color\]~si'    => '<span style="color:$1;">$2</span>',
	'~\[url\]((?:ftp|https?)://.*?)\[/url\]~si'                         => '<a href="$1">$1</a>',
	'~\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~si'       => '<p><img class="user_image_link" src="$1" alt="" /></p>',
        '~\[img w=((?:[1-9][0-9]?[0-9]?))\](.*?)\[\/img\]~si'               => '<p><img class="user_image_link" width="$1" src="$2" alt="" /></p>',
        '~\[localimg\](.*?)\[\/localimg\]~si'                               => '<p><img class="user_image_link" src="{STATIC_SRV_URL}$1" alt="" /></p>',
        '~\[localimg w=((?:[1-9][0-9]?[0-9]?))\](.*?)\[\/localimg\]~si'     => '<p><img class="user_image_link" width="$1" src="{STATIC_SRV_URL}$2" alt="" /></p>',
        '~\[list\](.*?)\\[\\/list\\]~si'                                    => '<ol>$1</ol>',
        '~\[\*\](.*)\[\/\*\]~i'                                             => '<li>$1</li>',
        '~\[style=((?:[a-zA-Z-_:;])+)\]~si'                                 => '<div style="$1">',
        '~\[/style\]~si'                                                    => '</div>',
        '~\[h2\](.*?)\[/h2\]~si'                                            => '<h2>$1</h2>',
        '~\[h3\](.*?)\[/h3\]~si'                                            => '<h3>$1</h3>',
        '~\[h4\](.*?)\[/h4\]~si'                                            => '<h4>$1</h4>',                
        '~\[div_class=((?:[a-zA-Z-_\s])+)\](.*?)\[/div_class\]~si'          => '<div class="$1">$2</div>',
        '~\[blockquote\](.*?)\[/blockquote\]~si'                            => '<blockquote>$1</blockquote>',
        '~\[code\](.*?)\[/code\]~si'                                        => '<code>$1</code>',
        '~\[br\]~si'                                                              => '<br/>',
    );

    function parse($text) {
        global $config;
        $text = preg_replace(array_keys($this->bbcode), array_values($this->bbcode), $text);
        $text = nl2br($text);
        $text = preg_replace("/><br \/>(\s*)(<br \/>)?/si", ">" , $text);
        $text = preg_replace('/{STATIC_SRV_URL}/si', $config['STATIC_SRV_URL'], $text);
        $text = preg_replace('/\[S\]/si', DIRECTORY_SEPARATOR . $config['IMG_SELECTOR'] . DIRECTORY_SEPARATOR , $text);
        return  $text;
    }
}