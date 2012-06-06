<?php

class Crawler {
    
    public static function userShowAPI($username, $entities = true) {
        $url = "http://api.twitter.com/1/users/show.json";
        $screen_name = "screen_name=".$username;
        $include = "include_entities=".$entities;
        $url = $url."?".$screen_name."&".$include;
        $contents = self::getURLContents($url);
        $data = json_decode($contents);
        return $data;
    }
    
    public static function getFriendsAPI($username, $cursor = -1) {
        $url = "http://api.twitter.com/1/friends/ids.json";
        $c = "cursor=".$cursor;
        $screen_name = "screen_name=".$username;
        $url = $url."?".$c."&".$screen_name;
        $contents = self::getURLContents($url);
        $data = json_decode($contents);
        return $data;
    }
    
    public static function getFollowersAPI($username, $cursor = -1) {
        $url = "http://api.twitter.com/1/followers/ids.json";
        $c = "cursor=".$cursor;
        $screen_name = "screen_name=".$username;
        $url = $url."?".$c."&".$screen_name;
        $contents = self::getURLContents($url);
        $data = json_decode($contents);
        return $data;
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