<?php
require_once(ROOT_PATH."/controller/class.DPController.php");
require_once(ROOT_PATH."/model/class.StatusProcessing.php");

class SentimentAnalysisController extends DPController {
    
    public function go() {
        
        $statuses = json_decode($_POST['statuses']);
        $array = self::Crawl($statuses);
        
        $this->addToView('count', $array['count']);
        $this->addToView('sentiment', $array['sentiment']);
        $this->addToView('min', $array['min']);
        $this->addToView('min_tweets', $array['min_tweets']);
        $this->addToView('max', $array['max']);
        $this->addToView('max_tweets', $array['max_tweets']);
        $this->addToView('pos_percent', $array['pos_percent']);
        
        $this->setViewTemplate('sentiment.tpl');
        return $this->generateView();
    }
    
    private static function crawl($statuses) {
        $sentiments = StatusProcessing::findSentiment($statuses);
        asort($sentiments);
        $count = count($sentiments);
        $sum = array_sum($sentiments);
        $sentiment = ($sum/$count);
        $sentiment = round((1+$sentiment)*50);
        $count_neg = 0;
        $tweet_count = 10;
        $i = 0;
        $min = array();
        $max = array();
        foreach ($sentiments as $k => $v) {
            if ($v <= 0) $count_neg++;
            if ($i < $tweet_count) {
                array_push($min, $k);
            } elseif ($i > $count-$tweet_count-1) {
                array_push($max, $k);
            }
            $i++;
        }
        $count_pos = $count - $count_neg;
        $pos_percent = round(($count_pos/$count)*100);
        $min_vals = array();
        $min_tweets = array();
        $max_vals = array();
        $max_tweets = array();
        for ($i = 0; $i < $tweet_count; $i++) {
            array_push($min_vals, $sentiments[$min[$i]]);
            array_push($min_tweets, $statuses[$min[$i]]->text);
            array_push($max_vals, $sentiments[$max[$tweet_count-$i-1]]);
            array_push($max_tweets, $statuses[$max[$tweet_count-$i-1]]->text);
        }
        
        $array = array (
            'count' => $tweet_count,
            'sentiment' => $sentiment,
            'max' => $max_vals,
            'max_tweets' => $max_tweets,
            'min' => $min_vals,
            'min_tweets' => $min_tweets,
            'pos_percent' => $pos_percent
        );
        return $array;
    }
    
}