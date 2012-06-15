<?php
require_once(ROOT_PATH."/controller/class.DPController.php");

class LandingPageController extends DPController {
    
    public function go() {
        
        $this->setViewTemplate("landingpage.tpl");
        $this->generateView();
        
    }
    
}