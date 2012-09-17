<?php
/**
 *
 * Data-Portraits/model/class.UserProcessing.php
 * Model for processing User object
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
require_once(ROOT_PATH."/model/class.StatusProcessing.php");

class TopicModel {
    
    private $tweets = array();
    private $num = 6;
    private $topic_text = array();
    private $topic_text_values = array();
    
    const DIRECT_REPLY = 0;
    const INTERACTION = 1;
    const WITH_URL = 2;
    const WITH_HASHTAG = 3;
    const PERSONAL = 4;
    const OTHER = 5;
    
    public function finalStepTopicModelling($statuses, $topics) {
        $this->num = count($topics);
        $this->init();
        $this->tweets = $topics;
        $topic_status = array();
        for ($i = 0; $i < $this->num; $i++) {
            $topic_status[$i] = array();
        }
        $count_original_statuses = count($statuses);
        for ($i = 0; $i < $this->num; $i++) {
            foreach ($topics[$i] as $index) {
                if ($index >= $count_original_statuses) {
                    continue;
                }
                array_push($topic_status[$i], $statuses[$index]);
            }
        }
        for ($i = 0; $i < $this->num; $i++) {
            $this->analyzeText($topic_status[$i], $i);
        }
        $result = array(
            'num' => $this->num,
            'topic_text' => $this->topic_text,
            'topic_text_values' => $this->topic_text_values,
            'tweets' => $this->tweets
        );
        return $result;
    }
    
    public function analyse($statuses) {
        $this->init();
        
        $direct_reply = array();
        $interactions = array();
        $with_urls = array();
        $with_hashtags = array();
        $personal = array();
        $others = array();
        
        $pronouns = self::getPronouns();
        $i = 0;
        foreach ($statuses as $status) {
            $flag = 0;
            if ($status->urls) {
                array_push($with_urls, $status);
                array_push($this->tweets[self::WITH_URL], $i);
                $flag = 1;
            }
            if ($status->hashtags) {
                array_push($with_hashtags, $status);
                array_push($this->tweets[self::WITH_HASHTAG], $i);
                $flag = 1;
            }
            if ($status->mentions) {
                if ($status->text[0] == "@") {
                    array_push($direct_reply, $status);
                    array_push($this->tweets[self::DIRECT_REPLY], $i);
                }
                if ($status->text[0] != "@" || count($status->mentions) > 1) {
                    array_push($interactions, $status);
                    array_push($this->tweets[self::INTERACTION], $i);
                }
                $flag = 1;
            }
            $words = explode(" ", $status->text_processed);
            foreach ($words as $word) {
                if (in_array($word, $pronouns)) {
                    $flag = 1;
                    array_push($personal, $status);
                    array_push($this->tweets[self::PERSONAL], $i);
                    break;
                }
            }
            if (!$flag) {
                array_push($others, $status);
                array_push($this->tweets[self::OTHER], $i);
            }
            $i++;
        }
        $this->analyzeText($direct_reply, self::DIRECT_REPLY);
        $this->analyzeText($interactions, self::INTERACTION);
        $this->analyzeText($with_urls, self::WITH_URL);
        $this->analyzeText($with_hashtags, self::WITH_HASHTAG);
        $this->analyzeText($personal, self::PERSONAL);
        $this->analyzeText($others, self::OTHER);
        
        $result = array(
            'num' => $this->num,
            'topic_text' => $this->topic_text,
            'topic_text_values' => $this->topic_text_values,
            'tweets' => $this->tweets
        );
        return $result;
    }
    
    protected function init() {
        for ($i = 0; $i < $this->num; $i++) {
            $this->tweets[$i] = array();
            $this->topic_text[$i] = array();
            $this->topic_text_values[$i] = array();
        }
    }
    
    private static function getPronouns() {
        return array(
            'i', 'me', 'mine', 'my', 'myself',
            'we', 'us', 'our', 'ours', 'ourselves'
        );
    }
    
    private function compare($a, $b) {
        return $a['total'] < $b['total'];
    }
    
    protected function analyzeText($statuses, $category) {
        $words = StatusProcessing::findWords($statuses);
        uasort($words, array($this, 'compare'));
        array_splice($words, 15);
        foreach ($words as $word => $value) {
            array_push($this->topic_text[$category], $word);
            array_push($this->topic_text_values[$category], $value['total']);
        }
        return $words;
    }
}