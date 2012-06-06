<?php

class TwitterCrawlerController {
    
    public function go() {
        
        function getConnectionWithAccessToken($oauth_token, $oauth_token_secret) {
            $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_KEY_SECRET,
                                           $oauth_token,
                                           $oauth_token_secret);
            return $connection;
        }
      
        $token_r = $_SESSION['oauth_token'];
        $secret_r = $_SESSION['oauth_secret'];
        
        $connection = getConnectionWithAccessToken($token_r,                                            $secret_r);
        $content = $connection->get("statuses/home_timeline");
        
        $nettuts_timeline = $connection->get('statuses/user_timeline',
                                             array('screen_name' => 'nettuts'));
        
        print_r($nettuts_timeline);
        
    }
}