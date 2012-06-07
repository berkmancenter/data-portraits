<?php

class Loader {
    
    public static function defineConstants() {
        
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH',dirname(dirname(__FILE__)));
        }
        
    }
    
}