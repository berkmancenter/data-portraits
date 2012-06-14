<?php
/**
 *
 * /model/class.SmartyDP.php
 *
 * Data Portrait's Smarty object
 *
 * Configures and initalizes Smarty per Data Portrait's configuration.
 *
 */
require_once(ROOT_PATH."/config.inc.php");
require_once(ROOT_PATH."/extlib/Smarty-3.1.8/libs/Smarty.class.php");

class SmartyDP extends Smarty {
    
    /**
     * @var boolean
     */
    private $debug = false;
    
    /**
     * @var array
     */
    private $template_data = array();
    
    /**
     * Constructor to initialize SmartyDP
     *
     * @param array $config_array Defaults to null;
     *
     */
    public function __construct() {
        $site_root_path = SITE_ROOT_PATH;
        $app_title = APP_TITLE;
	$debug=DEBUG;
	$cache_pages=CACHE_PAGES;
        
        Smarty::__construct();
        $this->template_dir = ROOT_PATH.'/view';
        $this->compile_dir = ROOT_PATH.'/view/compiled_view';
        $this->cache_dir = ROOT_PATH.'/cache';
        $this->caching = ($cache_pages)?1:0;
        $this->cache_lifetime = 300;
        $this->debug = $debug;
        $this->assign('app_title', $app_title);
        $this->assign('site_root_path', $site_root_path);
    }   
    
    /**
     * Assigns data to a template variable.
     * If debug is true, stores it for access by tests or developer.
     * @param string $key
     * @param mixed $value
     */
    public function assign($key, $value = null) {
        parent::assign($key, $value);
        if ($this->debug) {
            $this->template_data[$key] = $value;
        }
    }
    
    /**
     * For use only by tests: return a template data value by key.
     * @param string $key
     */  
    public function getTemplateDataItem($key) {
        return isset($this->template_data[$key]) ? $this->template_data[$key]:null;
    }

    /**
     * Check if caching is enabled
     * @return bool
     */
    public function isViewCached() {
        return ($this->caching==1)?true:false;
    }

    /**
     * Turn off caching
     */
    public function disableCaching() {
        $this->caching=0;
    }

    /**
     * Override the parent's clear_all_cache method to check if caching is on to begin with. We do this to prevent the
     * cache/MAKETHISDIRWRITABLE.txt from being deleted during test runs; this file needs to exist in order for the
     * cache directory to remain in the git repository.
     * @param int $expire_time
     */
    public function clear_all_cache($exp_time = null) {
        if ($this->caching == 1) {
            parent::clear_all_cache($exp_time);
        }
    }
    

}