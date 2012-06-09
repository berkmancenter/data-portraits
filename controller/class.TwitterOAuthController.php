<?php
require_once("init.php");
require_once(ROOT_PATH."/model/class.UsersTable.php");

class TwitterOAuthController {
    
    public function go() {
        if (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) &&
            !empty($_SESSION['oauth_token_secret'])) {
            
            // TwitterOAuth instance, with two new parameters we got in twitter_login.php
            $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_KEY_SECRET,
                                             $_SESSION['oauth_token'],
                                             $_SESSION['oauth_token_secret']);
            
            // request the access token
            $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
            
            // Saving it in a session var
            $_SESSION['access_token'] = $access_token;
            
            $user_info = $twitteroauth->get('account/verify_credentials');
            
            if(isset($user_info->error)) {
                // Error
                header('Location: '.LOGIN_LINK);
            } else {
                // find the user by its ID
                $result = UsersTable::findByID($user_info->id);
                
                $vals = array(
                    'oauth_token' => $access_token['oauth_token'],
                    'oauth_token_secret' => $access_token['oauth_token_secret'],
                    'id' => $user_info->id,
                    'screen_name' => $user_info->screen_name
                );
                
                // If not, add it to the database
                if(empty($result)){
                    $result = UsersTable::addUser($vals);
                } else {
                    // Update the tokens
                    $query = UsersTable::updateUser($vals);
                }
            
                $_SESSION['id'] = $result['id'];
                $_SESSION['username'] = $result['username'];
                $_SESSION['oauth_uid'] = $result['oauth_uid'];
                $_SESSION['oauth_provider'] = $result['oauth_provider'];
                $_SESSION['oauth_token'] = $result['oauth_token'];
                $_SESSION['oauth_secret'] = $result['oauth_secret'];
            
                if(!empty($_SESSION['username'])){
                    // User is logged in, redirect
                    header('Location: '.CRAWLER_LINK);
                }
            }
            
        } else {
            // error
            header('Location: '.LOGIN_LINK);
        }
    }
}

