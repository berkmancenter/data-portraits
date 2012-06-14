<?php

class LookupTable {
    
    public static function userExists($user) {
        global $db;
        $sql = "SELECT * FROM lookup WHERE username='".$user."';";
        $query = $db->query($sql);
        $result = $db->numRows($query);
        return $result;
    }
    
    public static function insertUser($user, $date) {
        global $db;
        $sql = "INSERT INTO lookup(username, last_crawl) VALUES
                ('".$user."', '".$date."');";
        $query = $db->query($sql);
        $result = $db->affectedRows();
        return $result;
    }
    
    public static function checkLastCrawl($user, $date) {
        global $db;
        $sql = "SELECT * FROM lookup WHERE username='".$user."'
                AND last_crawl >= '".$date."'";
        $query = $db->query($sql);
        $result = $db->numRows($query);
        return $result;
    }
    
    public static function deleteUser($user) {
        global $db;
        $sql = "DELETE FROM lookup WHERE username='$user'";
        $query = $db->query($sql);
        $result = $db->affectedRows($query);
        return $result;
    }
    
}