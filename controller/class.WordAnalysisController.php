<?php
require_once(ROOT_PATH."/controller/class.DPController.php");
require_once(ROOT_PATH."/model/class.StatusProcessing.php");

class WordAnalysisController extends DPController {
    
    public function go() {
        
        $statuses = json_decode($_POST['statuses']);
        $array = self::Crawl($statuses);
        
        $words = 'var words = ';
        $words .= json_encode($array['words']);
        
        $this->addToView('words', $words);
        $this->addToView('max', $array['max']);
        $this->addToView('count', $array['count']);
        $this->addToView('avg', $array['avg']);
        $this->addToView('time_taken', $array['time_taken']);
        
        $this->setViewTemplate('wordanalysis.tpl');
        return $this->generateView();
    }
    
    private static function crawl($user_timeline) {
        
        $count = StatusProcessing::getNumberOfStatuses($user_timeline);
        $time_taken = StatusProcessing::getNumberOfDays(
                      $user_timeline[0], $user_timeline[$count-1]);
        $words = StatusProcessing::findWords($user_timeline, $max, $avg);
        
        //$sentiment = StatusProcessing::findSentiment($user_timeline, $count);
        //echo $sentiment;
        
        // Anil Dash
        //$count = 173;
        //$time_taken = 10;
        //$max = 12;
        //$avg = 1.2855;
        
        // Gina Trapani
        //$count = 183;
        //$time_taken = 36;
        //$max = 12;
        //$avg = 1.37;
        
        //$words = 0;
        
        $array = array (
            'words' => $words,
            'max' => $max,
            'count' => $count,
            'time_taken' => $time_taken,
            'avg' => $avg
        );
        
        return $array;
    }
}