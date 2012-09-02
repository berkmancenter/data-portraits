<?php
/**
 *
 * Data-Portraits/controller/class.WordAnalysisController.php
 * Class for showing Word Analysis metric
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
require_once(ROOT_PATH."/model/class.Crawler.php");

class WordAnalysisController extends DPController {
    
    public function go() {
        
        if (isset($_POST["type"]) && $_POST["type"]=="connection") {
            if (isset($_POST["statuses"])) {
                
            } else {
                $authentication = array(
                    'token' => $_SESSION['oauth_token'],
                    'token_secret' => $_SESSION['oauth_secret']
                );
                if (isset($_POST['left'])) {
                    $this->addToView('pos_left', $_POST['left']);
                }
                if (isset($_POST['top'])) {
                    $this->addToView('pos_top', $_POST['top']);
                }
                if (isset($_POST['relation'])) {
                    $this->addToView('relation', $_POST['relation']);
                }
                $vals = array(
                    'screen_name' => $_POST["username"]
                );
                $connection = new Crawler($authentication);
                $statuses = StatusProcessing::getUserTimeline($connection, $vals);
                $array = self::crawl($statuses);
            }
        } else {
            if (isset($_POST['statuses'])) {
                $statuses = json_decode($_POST['statuses']);
                $array = self::Crawl($statuses);
            } else {
                $array = self::forwardData();
            }
        }
        
        $this->addToView('words', $array['words']);
        $this->addToView('max', $array['max']);
        $this->addToView('count', $array['count']);
        $this->addToView('avg', $array['avg']);
        $this->addToView('time_taken', $array['time_taken']);
        
        if (isset($_POST['type']) && $_POST['type'] == "connection") {
            $this->setViewTemplate('wordanalysis_secondary.tpl');
        } else {
            $this->setViewTemplate('wordanalysis.tpl');
        }
        return $this->generateView();
    }
    
    public static function crawl($user_timeline) {
        
        $count = StatusProcessing::getNumberOfStatuses($user_timeline);
        $time_taken = StatusProcessing::getNumberOfDays(
                      $user_timeline[0], $user_timeline[$count-1]);
        $words = StatusProcessing::findWords($user_timeline);
        $stats = self::findMaxAndAvg($words);
        
        $words = 'var words = '.json_encode($words).";";
        $max= $stats['max'];
        $avg = $stats['avg'];
        
        // Anil Dash
        //$count = 173;
        //$time_taken = 10;
        //$max = 12;
        //$avg = 1.2855;
        
        // Gina Trapani
        //$count = 183;
        //$time_taken = 36;
        //$max = 12;
        //$avg = 1.37;
        
        //$words = 0;
        
        $array = array (
            'words' => $words,
            'max' => $max,
            'count' => $count,
            'time_taken' => $time_taken,
            'avg' => $avg
        );
        return $array;
    }
    
    private static function forwardData() {
        if (get_magic_quotes_gpc()) {
            $words = "var words = ".stripcslashes($_POST['words']).";";
        } else {
            $words = "var words = ".$_POST['words'].";";
        }
        $max = $_POST['max'];
        $count = $_POST['count'];
        $time_taken = $_POST['time_taken'];
        $avg = $_POST['avg'];
        $array = array (
            'words' => $words,
            'max' => $max,
            'count' => $count,
            'time_taken' => $time_taken,
            'avg' => $avg
        );
        return $array;
    }
    
    private function findMaxAndAvg($words) {
        $max = 0;
        $number_of_instances = 0;
        $number_of_words = 0;
        foreach($words as $word) {
            $number_of_words++;
            $number_of_instances += $word['total'];
            if ($word['total'] > $max) {
                $max = $word['total'];
            }
        }
        $avg = $number_of_instances / $number_of_words;
        $array = array(
            'max' => $max,
            'avg' => $avg
        );
        return $array;
    }
}