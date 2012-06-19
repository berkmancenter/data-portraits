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
        $bigrams_list = array();
        $trigrams_list = array();
        $fourgrams_list = array();
        $fivegrams_list = array();
        
        $status_no = 0;

        // Complete unigram processing
        foreach ($statuses as $status) {
            $text = $status->text_processed;
            $words = explode(" ", $text);
            $unigrams = array();
            foreach ($words as $word) {
                if (!in_array($word, $stop_words) && strlen($word)>1) {
                    $unigrams[] = $word;
                    if (isset($words_list[$word])) {
                        $count_instances++;
                        $words_list[$word]['total']++;
                        if ($status->urls) {
                            $words_list[$word]['url']++;
                        }
                        if ($words_list[$word]['total'] > $maximum) {
                            $maximum = $words_list[$word]['total'];
                        }
                    } else {
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

            $bigrams = array();
            $count = count($unigrams);
            // Bigram processing step 1
            for ($i = 0; $i < $count-1; $i++) {
                $bigram = $unigrams[$i]." ".$unigrams[$i+1];
                $bigrams[] = $bigram;
                if (isset($bigrams_list[$bigram])) {
                    $bigrams_list[$bigram]['total']++;
                    $bigrams_list[$bigram]['index'][] = $status_no;
                    if ($status->urls) {
                        $bigrams_list[$bigram]['url']++;
                    }
                } else {
                    $bigrams_list[$bigram]['total'] = 1;
                    $bigrams_list[$bigram]['url'] = 0;
                    $bigrams_list[$bigram]['index'] = array();
                    $bigrams_list[$bigram]['index'][] = $status_no;
                    if ($status->urls) {
                        $bigrams_list[$bigram]['url']++;
                    }
                }
            }
            
            // Trigram processing step 1
            $trigrams = array();
            for ($i = 0; $i < $count-2; $i++) {
                $trigram = $unigrams[$i]." ".$unigrams[$i+1]." ".$unigrams[$i+2];
                $trigrams[] = $trigram;
                if (isset($trigrams_list[$trigram])) {
                    $trigrams_list[$trigram]['total']++;
                    $trigrams_list[$trigram]['index'][] = $status_no;
                    if ($status->urls) {
                        $trigrams_list[$trigram]['url']++;
                    }
                } else {
                    $trigrams_list[$trigram]['total'] = 1;
                    $trigrams_list[$trigram]['url'] = 0;
                    $trigrams_list[$trigram]['index'] = array();
                    $trigrams_list[$trigram]['index'][] = $status_no;
                    if ($status->urls) {
                        $trigrams_list[$trigram]['url']++;
                    }
                }
            }
            
            // Fourgram processing step 1
            $fourgrams = array();
            for ($i = 0; $i < $count-3; $i++) {
                $fourgram = $unigrams[$i]." ".$unigrams[$i+1]." ".$unigrams[$i+2]." ".$unigrams[$i+3];
                $fourgrams[] = $fourgram;
                if (isset($fourgrams_list[$fourgram])) {
                    $fourgrams_list[$fourgram]['total']++;
                    $fourgrams_list[$fourgram]['index'][] = $status_no;
                    if ($status->urls) {
                        $fourgrams_list[$fourgram]['url']++;
                    }
                } else {
                    $fourgrams_list[$fourgram]['total'] = 1;
                    $fourgrams_list[$fourgram]['url'] = 0;
                    $fourgrams_list[$fourgram]['index'] = array();
                    $fourgrams_list[$fourgram]['index'][] = $status_no;
                    if ($status->urls) {
                        $fourgrams_list[$fourgram]['url']++;
                    }
                }
            }
            
            // Fivegram processing step 1
            $fivegrams = array();
            for ($i = 0; $i < $count-4; $i++) {
                $fivegram = $unigrams[$i]." ".$unigrams[$i+1]." ".$unigrams[$i+2]." ".$unigrams[$i+3]." ".$unigrams;
                $fivegrams[] = $fivegram;
                if (isset($fivegrams_list[$fivegram])) {
                    $fivegrams_list[$fivegram]['total']++;
                    $fivegrams_list[$fivegram]['index'][] = $status_no;
                    if ($status->urls) {
                        $fivegrams_list[$fivegram]['url']++;
                    }
                } else {
                    $fivegrams_list[$fivegram]['total'] = 1;
                    $fivegrams_list[$fivegram]['url'] = 0;
                    $fivegrams_list[$fivegram]['index'] = array();
                    $fivegrams_list[$fivegram]['index'][] = $status_no;
                    if ($status->urls) {
                        $fivegrams_list[$fivegram]['url']++;
                    }
                }
            }
            $status_no++;
        }
        $avg = $count_instances / $count_distinct_words;
        unset($stop_words);
        
        $bigrams_final_list = self::processBigrams($statuses, $bigrams_list, $words_list, $maximum);
        unset($bigrams_list);
        
        $trigrams_final_list = self::processTrigrams($statuses, $trigrams_list, $bigrams_final_list);
        unset($trigrams_list);
        
        $fourgrams_final_list = self::processFourgrams($statuses, $fourgrams_list, $trigrams_final_list);
        unset($fourgrams_list);
        
        $fivegrams_final_list = self::processFivegrams($statuses, $fivegrams_list, $fourgrams_final_list);
        unset($fivegrams_list);
        
        foreach ($bigrams_final_list as $bigram=>$vals) {
            $new_vals = array('total' => $vals['total']*2, 'url' => $vals['url']*2);
            $words_list[$bigram] = $new_vals;
        }
        
        foreach ($trigrams_final_list as $trigram=>$vals) {
            $new_vals = array('total' => $vals['total']*3, 'url' => $vals['url']*3);
            $words_list[$trigram] = $new_vals;
        }
        
        foreach ($fourgrams_final_list as $fourgram=>$vals) {
            $new_vals = array('total' => $vals['total']*4, 'url' => $vals['url']*4);
            $words_list[$fourgram] = $new_vals;
        }
        
        foreach ($fivegrams_final_list as $fivegram=>$vals) {
            $new_vals = array('total' => $vals['total']*5, 'url' => $vals['url']*5);
            $words_list[$fivegram] = $new_vals;
        }
        
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
    
    private static function processBigrams($statuses, $bigrams_list, &$words_list, &$maximum) {
        $bigrams_final_list = array();
        // bigram processing step 2
        foreach ($bigrams_list as $bigram=>$b_vals) {
            if ($b_vals['total'] > 1) {
                $last_index = null;
                $new_index = array();
                $new_pos = array();
                $new_total = 0;
                $new_url = 0;
                $count = 0;
                foreach ($b_vals['index'] as $id) {
                    $text = strtolower($statuses[$id]->text);
                    if ($last_index!=null && $last_index == $id) {
                        $pos = strpos($text, $bigram, $pos + strlen($bigram));
                    } else {
                        $pos = strpos($text, $bigram);
                    }
                    if (!($pos===false)) {
                        $count++;
                        $new_index[] = $id;
                        $new_pos[] = $pos;
                        $new_total++;
                        if ($statuses[$id]->urls) {
                            $new_url++;
                        }
                    }
                    $last_index = $id;
                }
                if ($count > 1) {
                    $b_vals['total'] = $new_total;
                    $b_vals['url'] = $new_url;
                    $b_vals['index'] = $new_index;
                    $b_vals['pos'] = $new_pos;
                    $bigrams_final_list[$bigram] = $b_vals;
                }
            }
        }
        
        // Remove unigrams covered in bigrams
        foreach ($bigrams_final_list as $bigram=>$vals) {
            $words = explode(" ", $bigram);
            $unigram1 = $words[0];
            $unigram2 = $words[1];
            if (isset($words_list[$unigram1])
                && $maximum == $words_list[$unigram1]['total'] ) {
                $maximum -= $vals['total'];
            } elseif (isset($words_list[$unigram2])
                      && $maximum == $words_list[$unigram2]['total']) {
                $maximum -= $vals['total'];
            }
            if (isset($words_list[$unigram1])) {
                $words_list[$unigram1]['total'] -= $vals['total'];
                $words_list[$unigram1]['url'] -= $vals['url'];
                if ($words_list[$unigram1]['total'] < 2) {
                    unset($words_list[$unigram1]);
                }
            }
            if (isset($words_list[$unigram2])) {
                $words_list[$unigram2]['total'] -= $vals['total'];
                $words_list[$unigram2]['url'] -= $vals['url'];
                if ($words_list[$unigram2]['total'] < 2) {
                    unset($words_list[$unigram2]);
                }
            }
        }
        return $bigrams_final_list;
    }

    private static function processTrigrams($statuses, $trigrams_list, &$bigrams_final_list) {
        $trigrams_final_list = array();
        // trigram processing step 2
        foreach ($trigrams_list as $trigram=>$b_vals) {
            if ($b_vals['total'] > 1) {
                $last_index = null;
                $new_index = array();
                $new_pos = array();
                $new_total = 0;
                $new_url = 0;
                $count = 0;
                foreach ($b_vals['index'] as $id) {
                    $text = strtolower($statuses[$id]->text);
                    if ($last_index!=null && $last_index == $id) {
                        $pos = strpos($text, $trigram, $pos + strlen($trigram));
                    } else {
                        $pos = strpos($text, $trigram);
                    }
                    if (!($pos===false)) {
                        $count++;
                        $new_index[] = $id;
                        $new_pos[] = $pos;
                        $new_total++;
                        if ($statuses[$id]->urls) {
                            $new_url++;
                        }
                    }
                    $last_index = $id;
                }
                if ($count > 1) {
                    $b_vals['total'] = $new_total;
                    $b_vals['url'] = $new_url;
                    $b_vals['index'] = $new_index;
                    $b_vals['pos'] = $new_pos;
                    $trigrams_final_list[$trigram] = $b_vals;
                }
            }
        }
        
        // Remove bigrams covered in Trigrams
        foreach ($trigrams_final_list as $trigram=>$vals) {
            $words = explode(" ", $trigram);
            $bigram1 = $words[0]." ".$words[1];
            $bigram2 = $words[1]." ".$words[2];
            if (isset($bigrams_final_list[$bigram1])) {
                $bigrams_final_list[$bigram1]['total'] -= $vals['total'];
                $bigrams_final_list[$bigram1]['url'] -= $vals['url'];
                if ($bigrams_final_list[$bigram1]['total'] < 2) {
                    unset($bigrams_final_list[$bigram1]);
                }
            }
            if (isset($bigrams_final_list[$bigram2])) {
                $bigrams_final_list[$bigram2]['total'] -= $vals['total'];
                $bigrams_final_list[$bigram2]['url'] -= $vals['url'];
                if ($bigrams_final_list[$bigram2]['total'] < 2) {
                    unset($bigrams_final_list[$bigram2]);
                }
            }
        }
        return $trigrams_final_list;
    }
    
    private static function processFourgrams($statuses, $fourgrams_list, &$trigrams_final_list) {
        $fourgrams_final_list = array();
        // fourgram processing step 2
        foreach ($fourgrams_list as $fourgram=>$b_vals) {
            if ($b_vals['total'] > 1) {
                $last_index = null;
                $new_index = array();
                $new_pos = array();
                $new_total = 0;
                $new_url = 0;
                $count = 0;
                foreach ($b_vals['index'] as $id) {
                    $text = strtolower($statuses[$id]->text);
                    if ($last_index!=null && $last_index == $id) {
                        $pos = strpos($text, $fourgram, $pos + strlen($fourgram));
                    } else {
                        $pos = strpos($text, $fourgram);
                    }
                    if (!($pos===false)) {
                        $count++;
                        $new_index[] = $id;
                        $new_pos[] = $pos;
                        $new_total++;
                        if ($statuses[$id]->urls) {
                            $new_url++;
                        }
                    }
                    $last_index = $id;
                }
                if ($count > 1) {
                    $b_vals['total'] = $new_total;
                    $b_vals['url'] = $new_url;
                    $b_vals['index'] = $new_index;
                    $b_vals['pos'] = $new_pos;
                    $fourgrams_final_list[$fourgram] = $b_vals;
                }
            }
        }
        
        // Remove trigrams covered in fourgrams
        foreach ($fourgrams_final_list as $fourgram=>$vals) {
            $words = explode(" ", $fourgram);
            $trigram1 = $words[0]." ".$words[1]." ".$words[2];
            $trigram2 = $words[1]." ".$words[2]." ".$words[3];
            if (isset($trigrams_final_list[$trigram1])) {
                $trigrams_final_list[$trigram1]['total'] -= $vals['total'];
                $trigrams_final_list[$trigram1]['url'] -= $vals['url'];
                if ($trigrams_final_list[$trigram1]['total'] < 2) {
                    unset($trigrams_final_list[$trigram1]);
                }
            }
            if (isset($trigrams_final_list[$trigram2])) {
                $trigrams_final_list[$trigram2]['total'] -= $vals['total'];
                $trigrams_final_list[$trigram2]['url'] -= $vals['url'];
                if ($trigrams_final_list[$trigram2]['total'] < 2) {
                    unset($trigrams_final_list[$trigram2]);
                }
            }
        }
        return $fourgrams_final_list;
    }
    
    private static function processFivegrams($statuses, $fivegrams_list, &$fourgrams_final_list) {
        $fivegrams_final_list = array();
        // fivegram processing step 2
        foreach ($fivegrams_list as $fivegram=>$b_vals) {
            if ($b_vals['total'] > 1) {
                $last_index = null;
                $new_index = array();
                $new_pos = array();
                $new_total = 0;
                $new_url = 0;
                $count = 0;
                foreach ($b_vals['index'] as $id) {
                    $text = strtolower($statuses[$id]->text);
                    if ($last_index!=null && $last_index == $id) {
                        $pos = strpos($text, $fivegram, $pos + strlen($fivegram));
                    } else {
                        $pos = strpos($text, $fivegram);
                    }
                    if (!($pos===false)) {
                        $count++;
                        $new_index[] = $id;
                        $new_pos[] = $pos;
                        $new_total++;
                        if ($statuses[$id]->urls) {
                            $new_url++;
                        }
                    }
                    $last_index = $id;
                }
                if ($count > 1) {
                    $b_vals['total'] = $new_total;
                    $b_vals['url'] = $new_url;
                    $b_vals['index'] = $new_index;
                    $b_vals['pos'] = $new_pos;
                    $fivegrams_final_list[$fivegram] = $b_vals;
                }
            }
        }
        // Remove fourgrams covered in fivegrams
        foreach ($fivegrams_final_list as $fivegram=>$vals) {
            $words = explode(" ", $fivegram);
            $fourgram1 = $words[0]." ".$words[1]." ".$words[2]." ".$words[3];
            $fourgram2 = $words[1]." ".$words[2]." ".$words[3]." ".$words[4];
            if (isset($fourgrams_final_list[$fourgram1])) {
                $fourgrams_final_list[$fourgram1]['total'] -= $vals['total'];
                $fourgrams_final_list[$fourgram1]['url'] -= $vals['url'];
                if ($fourgrams_final_list[$fourgram1]['total'] < 2) {
                    unset($fourgrams_final_list[$fourgram1]);
                }
            }
            if (isset($fourgrams_final_list[$fourgram2])) {
                $fourgrams_final_list[$fourgram2]['total'] -= $vals['total'];
                $fourgrams_final_list[$fourgram2]['url'] -= $vals['url'];
                if ($fourgrams_final_list[$fourgram2]['total'] < 2) {
                    unset($fourgrams_final_list[$fourgram2]);
                }
            }
        }
        return $fivegrams_final_list;
    }
}