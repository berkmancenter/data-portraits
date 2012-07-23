<?php
/**
 *
 * Data-Portraits/model/class.SmartyDP.php
 * Data Portraits Smarty object. Configures Smarty as per Data Portrait's project.
 *
 * Copyright (c) 2012 Berkman Center for Internet and Society, Harvard Univesity
 *
 * LICENSE:
 *
 * This file is part of Data Portraits Project (http://cyber.law.harvard.edu/dataportraits/Main_Page).
 *
 * Data Portraits is a free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * Data Portraits is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with Data Portraits.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 *
 * @author Ekansh Preet Singh <ekanshpreet[at]gmail[dot]com>
 * @author Judith Donath <jdonath[at]cyber[dot]law[dot]harvard[dot]edu>
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2012 Berkman Center for Internet and Society, Harvard University
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