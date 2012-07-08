<?php
require_once(ROOT_PATH."/model/class.Crawler.php");
require_once(ROOT_PATH."/model/class.LookupTable.php");
require_once(ROOT_PATH."/model/class.UserDetailsTable.php");
require_once(ROOT_PATH."/model/class.UserProcessing.php");
require_once(ROOT_PATH."/model/class.StatusProcessing.php");
require_once(ROOT_PATH."/controller/class.DPController.php");

class TwitterCrawlerController extends DPController {
    
    public function go() {
        
        $_POST['username'] = 'judithd';
        
        if (!isset($_POST['username']) || $_POST['username'] == '') {
            header('Location: '.ROOT_PATH."/pages/home.php");
        }
        
        $array = self::newCrawl($_POST['username']);
        
        $user = $array['user'];
        $user_data = 'var user = ';
        $user_data .= json_encode($user);
        
        $statuses = 'var statuses = ';
        $statuses .= json_encode($array['user_timeline']);
        $this->addToView('user_data', $user_data);
        $this->addToView('statuses', $statuses);
        
        $words = 'var words = ';
        $words .= json_encode($array['words']);
        
        $this->addToView('words', $words);
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
            $user_timeline = StatusProcessing::getUserTimeline($connection, $vals);
            //$user_timeline = 0;
            
            $count = StatusProcessing::getNumberOfStatuses($user_timeline);
            $time_taken = StatusProcessing::getNumberOfDays(
                          $user_timeline[0], $user_timeline[$count-1]);
            $words = StatusProcessing::findWords($user_timeline, $max, $avg);
            
            $array = array(
                'user' => $user,
                'user_timeline' => $user_timeline,
                'words' => $words,
                'max' => $max,
                'count' => $count,
                'time_taken' => $time_taken,
                'avg' => $avg
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