<?php
/**
 *
 * Data-Portraits/controller/class.TwitterCrawlerController.php
 * Class for crawling Twitter for data.
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
require_once(ROOT_PATH."/model/class.Crawler.php");
require_once(ROOT_PATH."/model/class.LookupTable.php");
require_once(ROOT_PATH."/model/class.UserDetailsTable.php");
require_once(ROOT_PATH."/model/class.UserProcessing.php");
require_once(ROOT_PATH."/model/class.StatusProcessing.php");
require_once(ROOT_PATH."/controller/class.DPController.php");
require_once(ROOT_PATH."/controller/class.WordAnalysisController.php");

class TwitterCrawlerController extends DPController {
    
    public function go() {
        
        if (!isset($_POST['username']) || $_POST['username'] == '') {
            header('Location: '.ROOT_PATH."/pages/home.php");
        }
        
        $array = self::newCrawl($_POST['username']);
        
        $this->addToView('user_data', $array['user']);
        $this->addToView('statuses', $array['user_timeline']);
        $this->addToView('words', $array['words']);
        $this->addToView('max', $array['max']);
        $this->addToView('count', $array['count']);
        $this->addToView('avg', $array['avg']);
        $this->addToView('time_taken', $array['time_taken']);
        
        $this->setViewTemplate('visualize.tpl');
        $this->generateView();
        
    }
    
    private function newCrawl($username) {
        
        $authentication = array(
            'token' => $_SESSION['oauth_token'],
            'token_secret' => $_SESSION['oauth_secret']
        );
        
        $vals = array(
            'screen_name' => $username
        );
        $connection = new Crawler($authentication);
        $user = UserProcessing::getUserDetails($connection, $vals);
        if (is_array($user)) {
            // It should be an object, hence error
            self::identifyError($user['e_code']);
        } else {
            $user_json = "var user = ".json_encode($user).";";
            $user_timeline = StatusProcessing::getUserTimeline($connection, $vals);
            $user_timeline_json = "var statuses = ".json_encode($user_timeline).";";
            //$user_timeline = 0;
            
            $words_data = WordAnalysisController::crawl($user_timeline);
            
            $array = array(
                'user' => $user_json,
                'user_timeline' => $user_timeline_json,
                'words' => $words_data['words'],
                'max' => $words_data['max'],
                'count' => $words_data['count'],
                'time_taken' => $words_data['time_taken'],
                'avg' => $words_data['avg']
            );
            return $array;
        }
        
    }
    
    private function crawl($username) {
        
        $authentication = array(
            'token' => $_SESSION['oauth_token'],
            'token_secret' => $_SESSION['oauth_secret']
        );
        
        /*if (LookupTable::userExists($username)) {
            $week_ago = mktime(0,0,0,date("m"),date("d")-7,date("Y"));
            $date = date("Y-m-d", $week_ago);
            if (LookupTable::checkLastCrawl($username, $date)) {
                // Retrieve Data
            
                $data = UserDetailsTable::retrieveData($username);
                    
                foreach($data as $value) {
                    //$userdata = json_decode($value['userdata']);
                    //print_r($value);
                    print_r(json_decode($value['3']));
                    echo "<br/><br/><br/>";
                }
            }
        } else {
            // Insert username in lookup table
            $date = date("Y-m-d");
            LookupTable::deleteUser($username);
            LookupTable::insertUser($username, $date);
            UserDetailsTable::deleteUserData($username);
            
            $vals = array(
                'screen_name' => $username
            );
            $connection = new Crawler($authentication);
            
            $user_timeline = $connection->userTimeline($vals);
            UserDetailsTable::addData($username, "user_timeline", $user_timeline);
            
            $retweets_by = $connection->retweetsByUser($vals);
            UserDetailsTable::addData($username, "retweets_by", $retweets_by);
            
            $retweets_to = $connection->retweetsToUser($vals);
            UserDetailsTable::addData($username, "retweets_to", $retweets_to);*/
            $vals = array(
                'screen_name' => $username
            );
            $connection = new Crawler($authentication);
            $user = UserProcessing::getUserDetails($connection, $vals);
            if (is_array($user)) {
                // It should be an object, hence error
                self::identifyError($user['e_code']);
            } else {
                // Data is fine, insert into DB.
                //UserDetailsTable::addData($username, "user_details", $user_details);
                
                //print_r($user);
                //echo "<br/><br/>";
                
                //$user_timeline = StatusProcessing::getUserTimeline($connection, $vals);
                
                //foreach ($user_timeline as $status) {
                //   echo $status->text."<br/>";
                //}
                
                //print_r($user_timeline);
                //UserDetailsTable::addData($username, "user_timeline", $user_timeline);
                //$data = UserDetailsTable::retrieveData($username);
                //print_r($data[0]['userdata']);
                
            }
        //}
        
        $user_timeline = 0;
        
        $array = array(
            'user' => $user,
            'user_timeline' => $user_timeline
        );
        return $array;
    }
    
    private function identifyError($code) {
        switch($code) {
            case 34: echo "Username doesn't exist"; break;
            case 130: echo "Twitter Over Capacity"; break;
            case 131: echo "Twitter internal error"; break;
        }
    }
    
}