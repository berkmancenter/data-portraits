<?php
/**
 *
 * Data-Portraits/setup/sentimentdictionary/class.SentimentDictionary.php
 * Model for constructing the sentiment dictionary. This doesn't need to be called everytime.
 * The dictionary has already been processed and stored in extlib/SentiWordNet.
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
class SentimentDictionaryConstructor {
    
    private $dict;
    
    public function __construct() {
        $counts = array();
        $file = fopen(ROOT_PATH."/extlib/SentiWordNet/SentiWordNet_3.0.0.txt", "r");
        while (!feof($file)) {
            $data = fgetcsv($file, 1000, "\t");
            $words_ranks = explode(' ', $data[4]);
            foreach ($words_ranks as $word_rank) {
                $words = explode('#', $word_rank);
                $count = count($words);
                for ($i=0; $i<$count; $i+=2) {
                    if (isset($this->dict[$words[$i]][$words[$i+1]])) {
                        $this->dict[$words[$i]][$words[$i+1]] += $data[2]-$data[3];
                        $counts[$words[$i]][$words[$i+1]]++;
                    } else {
                        $this->dict[$words[$i]][$words[$i+1]] = $data[2]-$data[3];
                        $counts[$words[$i]][$words[$i+1]] = 1;
                    }
                }
            }
        }
        foreach ($counts as $word => $data) {
            foreach ($data as $rank => $count) {
                $this->dict[$word][$rank] /= $count;
            }
        }
    }
    
    public function getWordSentiment($word) {
        if (isset($this->dict[$word])) {
            $score = 0;
            $sum = 0;
            foreach ($this->dict[$word] as $k => $v) {
                // Give weightage to rank as well
                $score += (1.0/$k)*$v;
                $sum += (1.0/$k);
            }
            $score /= $sum;
            return $score;
        } else {
            return 0;
        }
    }
    
    public function createDictionary() {
        $file2 = fopen(ROOT_PATH."/extlib/SentiWordNet/SentiWordNet_calculated.txt", "w");
        foreach ($this->dict as $word=>$v) {
            $senti = $this->getWordSentiment($word);
            if ($senti) {
                $str = $word."\t".$senti."\n";
                fputs($file2, $str);
            }
        }
    }
}