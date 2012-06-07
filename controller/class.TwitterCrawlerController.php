<?php
require_once(ROOT_PATH."/model/class.Crawler.php");
require_once(ROOT_PATH."/model/class.LookupTable.php");
require_once(ROOT_PATH."/model/class.UserDetailsTable.php");

class TwitterCrawlerController {
    
    public function go() {
        
        $authentication = array(
            'token' => $_SESSION['oauth_token'],
            'token_secret' => $_SESSION['oauth_secret']
        );
        
        $username = 'ginatrapani';
        
        if (LookupTable::userExists($username)) {
            $week_ago = mktime(0,0,0,date("m"),date("d")-7,date("Y"));
            $date = date("Y-m-d", $week_ago);
            if (LookupTable::checkLastCrawl($username, $date)) {
                // Retrieve Data
            
                $data = UserDetailsTable::retrieveData($username);
                    
                foreach($data as $value) {
                    print_r($value);
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
            UserDetailsTable::addData($username, "retweets_to", $retweets_to);
            
            $user_details = $connection->userDetails($vals);
            UserDetailsTable::addData($username, "user_details", $user_details);
        }
    }
    
}