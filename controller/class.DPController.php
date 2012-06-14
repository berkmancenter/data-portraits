<?php
/**
 *
 * Hackademic Controller
 *
 * The parent class of all Hackademic webapp controllers.
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