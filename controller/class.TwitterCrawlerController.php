<?php
require_once(ROOT_PATH."/model/class.Crawler.php");
require_once(ROOT_PATH."/model/class.LookupTable.php");
require_once(ROOT_PATH."/model/class.UserDetailsTable.php");
require_once(ROOT_PATH."/model/class.UserProcessing.php");
require_once(ROOT_PATH."/model/class.StatusProcessing.php");

class TwitterCrawlerController {
    
    public function go() {
        
        $_POST['username']=isset($_POST['username'])?$_POST['username']:'ginatrapani';
        
        if ($_POST['username'] == '') {
            header('Location: '.ROOT_PATH."/pages/home.php");
        }
        
        $authentication = array(
            'token' => $_SESSION['oauth_token'],
            'token_secret' => $_SESSION['oauth_secret']
        );
        
        $username = $_POST['username'];
        
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
                
                print_r($user);
                echo "<br/><br/>";
                
                $user_timeline = StatusProcessing::getUserTimeline($connection, $vals);
                
                /*foreach ($user_timeline as $status) {
                    echo $status->text."<br/>";
                    echo $status->text_processed."<br/><br/>";
                }*/
                
                //print_r(StatusProcessing::findMentions($user_timeline));
                //echo "<br/><br/>";
                //print_r(StatusProcessing::findHashTags($user_timeline));
                //echo "<br/><br/>";
                //print_r(StatusProcessing::findURLs($user_timeline));
                echo "<br/><br/>";
                print_r(StatusProcessing::findWords($user_timeline));
                //print_r($user_timeline);
                //UserDetailsTable::addData($username, "user_timeline", $user_timeline);
                //$data = UserDetailsTable::retrieveData($username);
                //print_r($data[0]['userdata']);
                
            }
        //}
    }
    
    private function identifyError($code) {
        switch($code) {
            case 34: echo "Username doesn't exist"; break;
            case 130: echo "Twitter Over Capacity"; break;
            case 131: echo "Twitter internal error"; break;
        }
    }
    
}