<?php
require_once(ROOT_PATH."/controller/class.DPController.php");

class LandingPageController extends DPController {
    
    public function go() {
        
        $this->setViewTemplate("view.tpl");
        $this->generateView();
        
    }
    
}