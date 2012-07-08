<?php
/**
 *
 * Data-Portraits/controller/class.DPController.php
 * Base class for all controllers
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
require_once(ROOT_PATH."/model/class.SmartyDP.php");

abstract class DPController {
    
    /**
     * @var Smarty Object
     */
    protected $smarty;
    
    /**
     * @var template path
     */
    protected $tmpl;
    
    /**
     * @var view template
     */
    protected $view_template;
    
    /**
     * @var array
     */
    protected $header_scripts = array ();
    
    /**
     * Constructor to initialize the Main Controller
     */
    public function __construct() {
        $this->smarty = new SmartyDP();        
    }
    
    /**
     * Add javascript to header
     *
     * @param str javascript path
     */
    public function addHeaderJavaScript($script) {
        array_push($this->header_scripts, $script);
    }
	
    /**
     * Set Page Title
     * @param $title str Page Title
     */
    public function addPageTitle($title) {
        self::addToView('controller_title', $title);
    }
    
    /**
     * Function to set view template
     * @param $tmpl str Template name
     */
    public function setViewTemplate($tmpl) {
	$this->view_template = ROOT_PATH.'/view/'.$tmpl;
   
    }
    
    /**
     * Generate View In Smarty
     */
    public function generateView() {
        $view_path = $this->view_template;
	$this->addToView('header_scripts', $this->header_scripts);
        return $this->smarty->display($view_path);
    }
        
    /**
     * Add error message to view
     * @param str $msg
     */
    public function addErrorMessage($msg) {
        $this->disableCaching();
        $this->addToView('errormsg', $msg );
    }

    /**
     * Add success message to view
     * @param str $msg
     */
    public function addSuccessMessage($msg) {
        $this->disableCaching();
        $this->addToView('successmsg', $msg );
    }
	
    /**
     * Function to add data to Smarty Template
     * @param $key str Variable name in Smarty
     * @param $value str Variable value in Smarty
     */
    public function addToView($key,$value) {
        $this->smarty->assign($key, $value);
    }
}