<?php

class Loader {
    
    public static function defineConstants() {
        
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH',dirname(dirname(__FILE__)));
        }
        
        if (!defined('LOGIN_LINK')) {
            define('LOGIN_LINK',SITE_ROOT_PATH."crawler/login.php");
        }
        
        if (!defined('REDIRECT_LINK')) {
            define('REDIRECT_LINK',SITE_ROOT_PATH."crawler/oauth.php");
        }
        
        if (!defined('CRAWLER_LINK')) {
            define('CRAWLER_LINK',SITE_ROOT_PATH."crawler/crawl.php");
        }
    }
    
}