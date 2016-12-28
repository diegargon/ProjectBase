
ProjectBase 
===========
Hello and excuse my english.
I started this project for learn how to build something like a CMS and framework from scratch, with plugins support, and other things for learning
more deeply PHP.

This code are in very early stage, have bugs, security problems and messy code. This code can change completely and probably will change. Not use it in
something serious.

EXAMPLE SITE
============
Early example  http://projectbase.envigo.net (sometimes not work since its the production web)

BUGS
=========
Android: JQUERY/JS Login, not prompt for remember.
and many other...

LICENSING
=========
TODO

REQUIREMENTS
============
TODO
/cache: (if CSS_OPTIMIZE)
/news_img: (for NewsUpload)

chown www-data:www-data ???? && chmod 755 ????

php-gd ||php5.6-gd

INSTALLATION
============
TODO

DEVELOPEMENT
============

Author
------

* Diego Garcia <diego@envigo.net>

Lastest add/canges? first latest
================================
* Rewrite SMBasic
* Support oauth (facebook only throught FB api)
* ReCaptcha plugin
    Recaptcha
* NewsMediaUpload
    Allow upload pictures for news
* NewsComments
    Add support for news comments using SimpleComents plugin
* SimpleComments 
    Comments base
* Google Analytcs
    Add news analytcs code 
* NewsVote
    Support Vote news
* NewsUserExtra
    Plugin for support extra features (atm only option display real name instead username), use UserExtra
* UserExtra 
    Extra fields base.
* NewsSearch
    Search features
* NewsAds
    News Ads support 
* SMBasicExtra
    Extra fields for user/profile
* SimpleCategories
    Categories features
* SimpleACL
    Support por ACL (Access Control List), simple and not tested enough
* DebugWindow
    Add simple window for DEBUG meesages
* Admin
    Admin features for plugins (need rewrite)
* ExampleWeb
    Entry default configuration web (news)
* Newspage
    News page style web
* Template 
    Plugin template
* Multilang
    Support website in multiple languages
* Plugin SMBasic
    Session Management support
* MysqlDB
    Mysql database 
*  tplbasic plugin
    Support templates
* Plugin support