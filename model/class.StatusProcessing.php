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
    
    public static function findWords($statuses, &$maximum, &$avg) {
        $words_list = array();
        $maximum = 0;
        $count_instances = 0;
        $count_distinct_words = 0;
        $stop_words = Utils::getStopWords();
        foreach ($statuses as $status) {
            $text = $status->text_processed;
            $text = strtolower(Utils::preprocessTweet($text));
            $words = explode(" ", $text);
            foreach ($words as $word) {
                if (isset($words_list[$word])) {
                    $count_instances++;
                    $words_list[$word]['total']++;
                    if ($status->urls) {
                        $words_list[$word]['url']++;
                    }
                    if ($words_list[$word]['total'] > $maximum) {
                        $maximum = $words_list[$word]['total'];
                    }
                } elseif (!in_array($word, $stop_words)) {
                    $count_distinct_words++;
                    $count_instances++;
                    $words_list[$word]['total'] = 1;
                    $words_list[$word]['url'] = 0;
                    if ($status->urls) {
                        $words_list[$word]['url']++;
                    }
                    if ($words_list[$word]['total'] > $maximum) {
                        $maximum = $words_list[$word]['total'];
                    }
                }
            }
        }
        $avg = $count_instances / $count_distinct_words;
        return $words_list; 
    }
    
    public static function getNumberOfStatuses($statuses) {
        return count($statuses);
    }
    
    public static function getNumberOfDays ($status1, $status2) {
        $date1 = self::convertDate($status1->created);
        $date2 = self::convertDate($status2->created);
        $diff = (strtotime($date1) - strtotime($date2))/(60*60*24);
        return $diff;
    }
    
    private static function convertDate($date_string) {
        $date = explode(" ", $date_string);
        $month = $date[1];
        switch ($month) {
            case 'Jan': $month = 1; break;
            case 'Feb': $month = 2; break;
            case 'Mar': $month = 3; break;
            case 'Apr': $month = 4; break;
            case 'May': $month = 5; break;
            case 'Jun': $month = 6; break;
            case 'Jul': $month = 7; break;
            case 'Aug': $month = 8; break;
            case 'Sep': $month = 9; break;
            case 'Oct': $month = 10; break;
            case 'Nov': $month = 11; break;
            case 'Dec': $month = 12; break;
        }
        return $date[2]."-".$month."-".$date[5];
    }
}