<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

class parse_text {
    
    private $bbcode = [
        '~\[p\](.*?)\[/p\]~si'                                              => '<p>$1</p>',
        '~\[b\](.*?)\[/b\]~si'                                              => '<span class="bold">$1</span>',
	'~\[i\](.*?)\[/i\]~si'                                              => '<span class="italic">$1</span>',
	'~\[u\](.*?)\[/u\]~si'                                              => '<span class="underline">$1</span>',
	'~\[pre\](.*?)\[/pre\]~si'                                          => '<pre>$1</pre>',
	'~\[size=((?:[1-9][0-9]?[0-9]?))\](.*?)\[/size\]~si'                => '<span style="font-size:$1px;">$2</span>',
	'~\[color=((?:[a-zA-Z]|#[a-fA-F0-9]{3,6})+)\](.*?)\[/color\]~si'    => '<span style="color:$1;">$2</span>',
        '~\[localimg\](.*?)\[\/localimg\]~si'                               => '<p><img class="user_image_link" src="{STATIC_SRV_URL}$1" alt="$1" /></p>',
        '~\[localimg w=((?:[1-9][0-9]?[0-9]?))\](.*?)\[\/localimg\]~si'     => '<p><img class="user_image_link" width="$1" src="{STATIC_SRV_URL}$2" alt="$2" /></p>',
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
        '~\[br\]~si'                                                        => '<br/>',
        '~\[youtube\]https:\/\/www.youtube.com\/watch\?v=(.*?)\[\/youtube\]~si' => '<div><iframe src="https://www.youtube.com/embed/$1" allowfullscreen></iframe></div>',
        '~\[youtube w=((?:[1-9][0-9]?[0-9]?)) h=((?:[1-9][0-9]?[0-9]?))\]https:\/\/www.youtube.com\/watch\?v=(.*?)\[\/youtube\]~si' => '<div><iframe width="$1" height="$2" src="https:\/\/www.youtube.com\/embed\/$3" frameborder="0" allowfullscreen></iframe></div>',
    ];

    function __construct() {
        global $cfg;
        if ($cfg['NEWS_PARSER_ALLOW_IMG']) {
            $this->bbcode['~\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~si'] = '<p><img class="user_image_link" src="$1" alt="" /></p>';
            $this->bbcode['~\[img w=((?:[1-9][0-9]?[0-9]?))\](.*?)\[\/img\]~si'] = '<p><img class="user_image_link" width="$1" src="$2" alt="" /></p>';
        }
        if ($cfg['NEWS_PARSER_ALLOW_URL']) {
            $this->bbcode['~\[url\]((?:ftps|https?)://.*?)\[/url\]~si'] = '<a rel="nofollow" target="_blank" href="$1">$1</a>';
            $this->bbcode['~\[url=((?:ftps?|https?)://.*?)\](.*?)\[/url\]~si'] = '<a rel="nofollow" target="_blank" href="$1">$2</a>';
        }
    }

    function parse($text) {
        global $cfg;
        $text = preg_replace(array_keys($this->bbcode), array_values($this->bbcode), $text);
        $text = nl2br($text);
        $text = preg_replace("/><br \/>(\s*)(<br \/>)?/si", ">", $text);
        $text = preg_replace('/{STATIC_SRV_URL}/si', $cfg['STATIC_SRV_URL'], $text);
        $text = preg_replace('/\[S\]/si', DIRECTORY_SEPARATOR . $cfg['IMG_SELECTOR'] . DIRECTORY_SEPARATOR, $text);
        return $text;
    }
}