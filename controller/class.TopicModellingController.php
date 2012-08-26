<?php
/**
 *
 * Data-Portraits/controller/class.TopicModellingController.php
 * Class for creating the Topic Modelling page
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
require_once(ROOT_PATH."/model/class.TopicModel.php");
require_once(ROOT_PATH."/model/class.StatusProcessing.php");

class TopicModellingController extends DPController {
    
    public function go() {
        if (get_magic_quotes_gpc()) {
            $statuses = json_decode(stripcslashes($_POST['statuses']));
        } else {
	    $statuses = json_decode($_POST['statuses']);
        }
	$topics = $this->performTopicModellingJava($statuses);
	//Old Stuff
        //$this->addToView('statuses', $statuses);
        //$this->setViewTemplate('topics.tpl');
        //return $this->generateView();
        $model = new TopicModel();
	//$result = $model->analyse($statuses);                               // Simpler Topic Modelling
	$result = $model->finalStepTopicModelling($statuses, $topics);        // With Java
	//$result = $model->analyse($statuses);
	$num = "var num = ".json_encode($result['num']).";";
	$tweets = "var tweets = ".json_encode($result['tweets']).";";
	$topic_text = "var topic_text = ".json_encode($result['topic_text']).";";
	$topic_text_values = "var topic_text_values = ".json_encode($result['topic_text_values']).";";
	$this->addToView('num', $num);
	$this->addToView('tweets', $tweets);
	$this->addToView('topic_text', $topic_text);
	$this->addToView('topic_text_values', $topic_text_values);
	$this->setViewTemplate('topics_new.tpl');
	return $this->generateView();
    }
    
    private function performTopicModellingJava($statuses, $topic_count = 10) {
	$documents = $this->extractDocuments($statuses);
	$count = count($documents);
	$username = $_SESSION['access_token']['screen_name'];
	$filename = ROOT_PATH."/data/topics/".$username.".txt";
	$hashmap = $this->generateHashMap($documents);
	$vocab_count = count($hashmap);
	$mapped_docs = $this->mapDocuments($documents, $hashmap);
	$this->putData($filename, $mapped_docs);
	$path = ROOT_PATH."/bin/topics/LDA.jar";
	$cmd = "java -jar $path $count $vocab_count $filename $topic_count";
	exec($cmd, $output);
	print_r($output);
	$topics = array();
        for ($i = 0; $i < $topic_count; $i++) {
            $topics[$i] = array();
        }
        $index = 0;
        $threshold = 10 / $topic_count;
	foreach ($output as $line) {
	    $topics_doc = explode(" ", $line);
            for ($i = 0; $i < $topic_count; $i++) {
                if ($topics_doc[$i] > $threshold) {
                    array_push($topics[$i], $index);
                }
            }
            $index++;
	}
	return $topics;
    }
    
    private function mapDocuments($documents, $hashmap) {
	$mapped = array();
	foreach ($documents as $document) {
	    $map = array();
	    foreach ($document as $word) {
		array_push($map, $hashmap[$word]);
	    }
	    array_push($mapped, $map);
	}
	return $mapped;
    }
    
    private function generateHashMap($documents) {
	$index = 0;
	$hashmap = array();
	foreach ($documents as $document) {
	    foreach ($document as $word) {
		if (!isset($hashmap[$word])) {
		    $hashmap[$word] = $index++;
		}
	    }
	}
	return $hashmap;
    }
    
    private function extractDocuments($statuses) {
	$documents = array();
	$words_list = StatusProcessing::findWords($statuses);
	$stopwords = Utils::getStopWords();
	foreach ($statuses as $status) {
	    $document = array();
	    $tweet = $status->text_processed;
	    $words = explode(" ", $tweet);
	    $word_count = count($words);
	    for ($i = 0; $i < $word_count; $i++) {
		if (isset($words_list[$words[$i]])) {
		    array_push($document, $words[$i]);
		} else if (in_array($words[$i], $stopwords)) {
		    continue;
		} else if ($i+1<$word_count && $words_list[$words[$i]." ".$words[$i+1]]) {
		    array_push($document, $words[$i]." ".$words[$i+1]);
		    $i += 1;
		} else if ($i+2<$word_count && $words_list[$words[$i]." ".$words[$i+1]." ".$words[$i+2]]) {
		    array_push($document, $words[$i]." ".$words[$i+1]." ".$words[$i+2]);
		    $i += 2;
		} else if ($i+3<$word_count && $words_list[$words[$i]." ".$words[$i+1]." ".$words[$i+2]." ".$words[$i+3]]) {
		    array_push($document, $words[$i]." ".$words[$i+1]." ".$words[$i+2]." ".$words[$i+3]);
		    $i += 3;
		} else if ($i+4<$word_count && $words_list[$words[$i]." ".$words[$i+1]." ".$words[$i+2]." ".$words[$i+3]." ".$words[$i+4]]) {
		    array_push($document, $words[$i]." ".$words[$i+1]." ".$words[$i+2]." ".$words[$i+3]." ".$words[$i+4]);
		    $i += 4;
		}
	    }
	    array_push($documents, $document);
	}
	return $documents;
    }
    
    private function putData($filename, $documents) {
	file_put_contents($filename, json_encode($documents));
    }
}