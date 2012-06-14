<?php

class UsersTable {
    
    public static function findByID($id) {
        global $db;
        $sql = "SELECT * FROM users WHERE oauth_provider = 'twitter' AND
                oauth_uid = ". $id;
        $query = $db->query($sql);
        $result = $db->fetchArray($query);
        return $result;
    }
    
    public static function addUser($vals) {
        global $db;
        $sql = "INSERT INTO users (oauth_provider, oauth_uid, username,
                oauth_token, oauth_secret) VALUES ('twitter', {$vals['id']},
                '{$vals['screen_name']}', '{$vals['oauth_token']}',
                '{$vals['oauth_token_secret']}')";
        $query = $db->query($sql);
        $sql = "SELECT * FROM users WHERE id = ".$db->insertID();
        $query = $db->query($sql);
        $result = $db->fetchArray($query);
        return $result;
    }
    
    public static function updateUser($vals) {
        global $db;
        $sql = "UPDATE users SET oauth_token = '{$vals['oauth_token']}',
                oauth_secret = '{$vals['oauth_token_secret']}' WHERE
                oauth_provider = 'twitter' AND oauth_uid = {$vals['id']}";
        $db->query($sql);
    }
    
}