<?php
require_once(ROOT_PATH."/model/class.User.php");

class UserProcessing {
    
    public static function getUserDetails($connection, $vals) {
        
        $user_details = $connection->userDetails($vals);
        if (isset($user_details->errors)) {
            $error['e_code'] = $user_details->errors[0]->code;
            return $error;
        }
        $user_details = $user_details[0];
        $user = new User($user_details);
        return $user;
    }
    
}