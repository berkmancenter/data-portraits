<?php
require_once(ROOT_PATH."/model/class.Status.php");
require_once(ROOT_PATH."/extlib/PorterStemmer/PorterStemmer.php");
require_once(ROOT_PATH."/model/class.Utils.php");

class StatusProcessing {
    
    public static function getUserTimeline($connection, $vals) {
        $vals['count'] = 200;
        $vals['include_entities'] = true;
        //$vals['include_rts'] = true;
        $timeline = $connection->userTimeline($vals);
        $statuses = array();
        $i=0;
        foreach ($timeline as $tweet) {
            $i++;
            $status = new Status($tweet);
            array_push($statuses, $status);
        }
        return $statuses;
    }
    
    public static function findHashTags($statuses) {
        $hashtags = array();
        foreach ($statuses as $status) {
            if($status->hashtags) {
                foreach($status->hashtags as $tag) {
                    if (isset($hashtags[$tag])) {
                        $hashtags[$tag]++;
                    } else {
                        $hashtags[$tag] = 1;
                    }
                }
            }
        }
        return $hashtags;
    }
    
    public static function findMentions($statuses) {
        $mentions = array();
        foreach ($statuses as $status) {
            if($status->mentions) {
                foreach($status->mentions as $tag) {
                    if (isset($mentions[$tag])) {
                        $mentions[$tag]++;
                    } else {
                        $mentions[$tag] = 1;
                    }
                }
            }
        }
        return $mentions;
    }
    
    public static function findURLs($statuses) {
        $urls = array();
        foreach ($statuses as $status) {
            if($status->urls) {
                foreach($status->urls as $tag) {
                    if (isset($urls[$tag])) {
                        $urls[$tag]++;
                    } else {
                        $urls[$tag] = 1;
                    }
                }
            }
        }
        return $urls;
    }
    
    public static function findWords($statuses) {
        $words_list = array();
        $stop_words = Utils::getStopWords();
        foreach ($statuses as $status) {
            $text = $status->text_processed;
            $text = strtolower(Utils::preprocessTweet($text));
            $words = explode(" ", $text);
            foreach ($words as $word) {
                if (isset($words_list[$word])) {
                    $words_list[$word]++;
                } elseif (!in_array($word, $stop_words)) {
                    $words_list[$word] = 1;
                }
            }
        }
        return $words_list; 
    }
}