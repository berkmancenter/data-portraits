<?php
/**
 *
 * Data-Portraits/controller/class.SentimentAnalysisController.php
 * Class for creating the Sentiment Analysis page.
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
require_once(ROOT_PATH."/controller/class.DPController.php");
require_once(ROOT_PATH."/model/class.StatusProcessing.php");

class SentimentAnalysisController extends DPController {
    
    public function go() {
        
        if (isset($_POST['statuses'])) {
            $statuses = json_decode($_POST['statuses']);
            $array = self::Crawl($statuses);
        } else {
            $array = self::forwardData();
        }
        $this->addToView('count', $array['count']);
        $this->addToView('sentiment', $array['sentiment']);
        $this->addToView('min', $array['min']);
        $this->addToView('min_json', json_encode($array['min']));
        $this->addToView('min_tweets', $array['min_tweets']);
        $this->addToView('min_tweets_json', json_encode($array['min_tweets']));
        $this->addToView('max', $array['max']);
        $this->addToView('max_json', json_encode($array['max']));
        $this->addToView('max_tweets', $array['max_tweets']);
        $this->addToView('max_tweets_json', json_encode($array['max_tweets']));
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
    
    private static function forwardData() {
        $tweet_count = $_POST['tweet_count'];
        $sentiment = $_POST['sentiment'];
        $max_vals = json_decode($_POST['max_vals']);
        $max_tweets = json_decode($_POST['max_tweets']);
        $min_vals = json_decode($_POST['min_vals']);
        $min_tweets = json_decode($_POST['min_tweets']);
        $pos_percent = $_POST['pos_percent'];
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