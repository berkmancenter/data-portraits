<?php

class Crawler {
    
    public $user;
    
    public $friends_ids;
    
    public $followers_ids;
    
    public function __construct($user = NULL) {
        if ($user != NULL) {
            $this->user = $user;
        }
    }
    
    public function getFriends() {
        $url = "http://api.twitter.com/1/friends/ids.json?cursor=-1&screen_name=";
        $url .= $this->user;
        $contents = self::getURLContents($url);
        $data = json_decode($contents);
        print_r($data);
    }
    
    public function getFollowers() {
        $url = "http://api.twitter.com/1/followers/ids.json?cursor=-1&screen_name=";
        $url .= $this->user;
        $contents = self::getURLContents($url);
        $data = json_decode($contents);
        print_r($data);
    }
    
    /**
     * Get the contents of a URL
     * @param str $URL
     * @return str contents
     */
    private static function getURLContents($URL) {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);
        if (isset($contents)) {
            return $contents;
        } else {
            return null;
        }
    }
}