<?php
require_once(ROOT_PATH."/controller/class.DPController.php");

class TopicModellingController extends DPController {
    
    public function go() {
        
        $statuses = "var statuses = ".$_POST['statuses'];
        
        $this->addToView('statuses', $statuses);
        $this->setViewTemplate('topics.tpl');
        return $this->generateView();
    }
    
}