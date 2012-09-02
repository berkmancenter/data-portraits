<?php
/**
 *
 * Data-Portraits/model/class.PartsOfSpeechTagger.php
 * Class for performing parts of speech tagging.
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
class PartsOfSpeechTagger {
    
    private $dict; 
    
    public function __construct() {
        $fh = fopen(ROOT_PATH."/data/lexicon.txt", 'r');
        while($line = fgets($fh)) {
            $tags = explode(' ', $line);
            $this->dict[strtolower(array_shift($tags))] = $tags;
        }
        fclose($fh);
    }
    
    public function tag($text) {
        preg_match_all("/[\w\d\.]+/", $text, $matches);
        $nouns = array('NN', 'NNS');
        
        $return = array();
        $i = 0;
        foreach($matches[0] as $token) {
            // default to a common noun
            $return[$i] = array('token' => $token, 'tag' => 'NN');  
            
            // remove trailing full stops
            if(substr($token, -1) == '.') {
                $token = preg_replace('/\.+$/', '', $token);
            }
            
            // get from dict if set
            if(isset($this->dict[strtolower($token)])) {
                $return[$i]['tag'] = $this->dict[strtolower($token)][0];
            }       
            
            // Converts verbs after 'the' to nouns
            if($i > 0) {
                if($return[$i - 1]['tag'] == 'DT' && 
                   in_array($return[$i]['tag'], 
                   array('VBD', 'VBP', 'VB'))) {
                    $return[$i]['tag'] = 'NN';
                }
            }
            
            // Convert noun to number if . appears
            if($return[$i]['tag'][0] == 'N' && strpos($token, '.') !== false) {
                $return[$i]['tag'] = 'CD';
            }
            
            // Convert noun to past particile if ends with 'ed'
            if($return[$i]['tag'][0] == 'N' && substr($token, -2) == 'ed') {
                $return[$i]['tag'] = 'VBN';
            }
            
            // Anything that ends 'ly' is an adverb
            if(substr($token, -2) == 'ly') {
                $return[$i]['tag'] = 'RB';
            }
            
            // Common noun to adjective if it ends with al
            if(in_array($return[$i]['tag'], $nouns) 
               && substr($token, -2) == 'al') {
                $return[$i]['tag'] = 'JJ';
            }
            
            // Noun to verb if the word before is 'would'
            if($i > 0) {
                if($return[$i]['tag'] == 'NN' 
                   && strtolower($return[$i-1]['token']) == 'would') {
                    $return[$i]['tag'] = 'VB';
                }
            }
            
            // Convert noun to plural if it ends with an s
            if($return[$i]['tag'] == 'NN' && substr($token, -1) == 's') {
                $return[$i]['tag'] = 'NNS';
            }
            
            // Convert common noun to gerund
            if(in_array($return[$i]['tag'], $nouns) && substr($token, -3) == 'ing') {
                $return[$i]['tag'] = 'VBG';
            }
            
            // If we get noun noun, and the second can be a verb, convert to verb
            if($i > 0) {
                if(in_array($return[$i]['tag'], $nouns) 
                   && in_array($return[$i-1]['tag'], $nouns) 
                   && isset($this->dict[strtolower($token)])) {
                    if(in_array('VBN', $this->dict[strtolower($token)])) {
                        $return[$i]['tag'] = 'VBN';
                    } else if(in_array('VBZ', 
                        $this->dict[strtolower($token)])) {
                        $return[$i]['tag'] = 'VBZ';
                    }
                }
            }
            $i++;
        }
        return $return;
    }
}