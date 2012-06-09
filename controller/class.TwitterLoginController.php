<?php

class TwitterLoginController {
    
    public function go() {        
        // The TwitterOAuth instance
        $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_KEY_SECRET);

        // Requesting authentication tokens, the parameter is the URL we will be redirected to
        $request_token = $twitteroauth->getRequestToken(REDIRECT_LINK);
        
        // Saving them into the session
        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
        
        // If everything goes well..
        if($twitteroauth->http_code==200) {
            // Generate the URL and redirect
            $url = $twitteroauth->getAuthorizeURL($_SESSION['oauth_token']);
            header('Location: '. $url);
        } else {
            die('Something wrong happened.');
        }
    }
}