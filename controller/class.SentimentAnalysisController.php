<?php
require_once(ROOT_PATH."/controller/class.DPController.php");
require_once(ROOT_PATH."/model/class.StatusProcessing.php");

class SentimentAnalysisController extends DPController {
    
    public function go() {
        
        $statuses = json_decode($_POST['statuses']);
        $array = self::Crawl($statuses);
        
        $this->addToView('sentiment', $array['sentiment']);
        $this->addToView('min', $array['min']);
        $this->addToView('min_tweet', $array['min_tweet']);
        $this->addToView('max', $array['max']);
        $this->addToView('max_tweet', $array['max_tweet']);
        $this->addToView('pos_percent', $array['pos_percent']);
        
        $this->setViewTemplate('sentiment.tpl');
        return $this->generateView();
    }
    
    private static function crawl($statuses) {
        $sentiments = StatusProcessing::findSentiment($statuses);
        $min = 0;
        $min_tweet = null;
        $max = 0;
        $max_tweet = null;
        $sum = 0;
        $count_pos = 0;
        foreach ($sentiments as $k=>$v) {
            $sum += $v;
            if ($v > 0) {
                $count_pos++;
            }
            if ($v > $max) {
                $max = $v;
                $max_tweet = $statuses[$k]->text;
            }
            if ($v < $min) {
                $min = $v;
                $min_tweet = $statuses[$k]->text;
            }
        }
        $sentiment = $sum/count($statuses);
        $pos_percent = ($count_pos*100)/count($statuses);
        
        $array = array (
            'sentiment' => $sentiment,
            'max' => $max,
            'max_tweet' => $max_tweet,
            'min' => $min,
            'min_tweet' => $min_tweet,
            'pos_percent' => $pos_percent
        );
        return $array;
    }
    
}