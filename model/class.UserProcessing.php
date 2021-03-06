<?php
/**
 *
 * Data-Portraits/model/class.UserProcessing.php
 * Model for processing User object
 *
 * Copyright (c) 2012 Berkman Center for Internet and Society, Harvard Univesity
 *
 * LICENSE:
 *
 * This file is part of Data Portraits Project (http://cyber.law.harvard.edu/dataportraits/Main_Page).
 *
 * Data Portraits is a free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * Data Portraits is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with Data Portraits.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 *
 * @author Ekansh Preet Singh <ekanshpreet[at]gmail[dot]com>
 * @author Judith Donath <jdonath[at]cyber[dot]law[dot]harvard[dot]edu>
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2012 Berkman Center for Internet and Society, Harvard University
 * 
 */
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
    
    public static function getUsersDetails($connection, $data, $type="ids") {
        $array = array();
        $chunks = array_chunk($data, 100);
        foreach ($chunks as $chunk) {
            if ($type == "ids") {
                $ids = implode(",",$chunk);
                $vals = array(
                    'user_id' => $ids
                );
            } else {
                $names = implode(",",$chunk);
                $vals = array(
                    'screen_name' => $names
                );
            }
            $user_details = $connection->multipleUsersDetails($vals);
            foreach ($user_details as $user_detail) {
                $user = new User($user_detail);
                array_push($array, $user);
            }
        }
        return $array;
    }
    
    public static function getRetweetersOfStatus($connection, $id) {
        $users = $connection->statusRetweetedBy($id);
        print_r($users);
    }
}