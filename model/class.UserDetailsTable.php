<?php

class UserDetailsTable {
    
    public static function addData($username, $type, $data) {
        global $db;
        $data = addslashes(json_encode($data));
        $sql = "INSERT INTO userdetails(username, type, userdata) VALUES
                ('$username','$type','$data');";
        $query = $db->query($sql);
        $result = $db->affectedRows($query);
        return $result;
    }
    
    public static function retrieveData($username) {
        global $db;
        $sql = "SELECT * FROM userdetails WHERE username='$username'";
        $query = $db->query($sql);
        $result = array();
        while ($row = $db->fetchArray($query)) {
            array_push($result, $row);
        }
        return $result;
    }
    
    public static function deleteUserData($username) {
        global $db;
        $sql = "DELETE FROM userdetails WHERE username='$username'";
        $query = $db->query($sql);
        $result = $db->affectedRows($query);
        return $result;
    }
    
}