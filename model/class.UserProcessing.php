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
require_once(ROOT_PATH."/model/class.StatusProcessing.php");

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
    
    private static function getUsersDetails($connection, $data, $type="ids") {
        $array = array();
        $chunks = array_chunk($data, 100);
        $i=0;
        foreach ($chunks as $chunk) {
            // Only allow maximum of 200 users
            if ($i>=2) {
                break;
            }
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
            $i++;
        }
        return $array;
    }
    
    public static function getConnections($connection, $vals, $statuses) {
        $friends_data = self::getAllFriends($connection, $vals);
        $followers_data = self::getAllFollowers($connection, $vals);
        $mutual_array = self::getAllMutuals($friends_data['assoc'], $followers_data['assoc']);
        $friends_array = $friends_data['normal'];
        $followers_array = $followers_data['normal'];
        $count_friends = count($friends_array);
        $count_followers = count($followers_array);
        unset($friends_data);
        unset($followers_data);
        unset($vals);
        
        $mutual_people = self::getUsersDetails($connection, $mutual_array);
        unset($mutual_array);
        
        // Select 200 friends - mentions plus remaining random friends
        $total = $count_friends>200?200:$count_friends;
        $mentions = StatusProcessing::findMentions($statuses);
        arsort($mentions);
        $mentions_array = array();
        foreach($mentions as $mention=>$count) {
            array_push($mentions_array, $mention);
        }
        $count_mentions = count($mentions_array);
        $friends_people = self::getUsersDetails($connection, $mentions_array, "screen_names");
        unset($mentions_array);
        unset($statuses);
        unset($mentions);

        $mentions_ids = array();
        foreach ($friends_people as $mention) {
            array_push($mentions_ids, $mention->id);
        }
        $base = rand(0, $count_friends>getrandmax()?getrandmax():$count_friends);
        $add = rand(1, 200);
        $remaining = $total - $count_mentions;
        $i = 0;
        $friends_ids = array();
        while ($i<$remaining) {
            $i++;
            $base = $base%$count_friends;
            while (in_array($friends_array[$base],$friends_ids) || in_array($friends_array[$base],$mentions_ids)) {
                $base++;
            }
            array_push($friends_ids,$friends_array[$base]);
            $base += $add;
        }
        unset($friends_array);
        unset($mentions_ids);
        $friends_temp_people = self::getUsersDetails($connection, $friends_ids);
        foreach ($friends_temp_people as $friend) {
            array_push($friends_people, $friend);
        }
        unset($friends_temp_people);
        
        // Select 200 random followers
        $base = rand(0, $count_followers>getrandmax()?getrandmax():$count_followers);
        $add = rand(1, 200);
        $i = 0;
        $total = $count_followers>200?200:$count_followers;
        $followers_ids = array();
        while ($i < $total) {
            $i++;
            $base %= $count_followers;
            while (in_array($followers_array[$base],$followers_ids)) {
                $base++;
            }
            array_push($followers_ids,$followers_array[$base]);
            $base += $add;
        }
        unset($followers_array);
        $followers_people = self::getUsersDetails($connection, $followers_ids);
        unset($followers_ids);
        
        
        $people = array(
            'friends' => $friends_people,
            'followers' => $followers_people,
            'mutuals' => $mutual_people
        );
        return $people;
    }
    
    private static function getAllMutuals($friends, $followers) {
        $mutual_array = array();
        foreach ($friends as $id=>$key) {
            if (isset($followers[$id])) {
                array_push($mutual_array, $id);
            }
        }
        return $mutual_array;
    }
    
    private static function getAllFollowers($connection, $vals) {
        $vals['cursor'] = -1;
        $followers_array_assoc = array();
        $followers_array_normal = array();
        $i = 0;
        while (1) {
            if ($i >= 11) {
                break;
            }
            $followers = $connection->getFollowers($vals);
            foreach ($followers->ids as $id) {
                $followers_array_assoc[$id] = true;
                array_push($followers_array_normal, $id);
            }
            if ($followers->next_cursor_str == 0) {
                break;
            } else {
                $vals['cursor'] = $followers->next_cursor_str;
            }
            $i++;
        }
        $followers_array = array(
            'assoc' => $followers_array_assoc,
            'normal' => $followers_array_normal
        );
        return $followers_array;
    }
    
    private static function getAllFriends($connection, $vals) {
        $vals['cursor'] = -1;
        $friends_array_assoc = array();
        $friends_array_normal = array();
        $i = 0;
        while (1) {
            if ($i >= 11) {
                break;
            }
            $friends = $connection->getFriends($vals);
            foreach ($friends->ids as $id) {
                $friends_array_assoc[$id] = true;
                array_push($friends_array_normal, $id);
            }
            if ($friends->next_cursor_str == 0) {
                break;
            } else {
                $vals['cursor'] = $friends->next_cursor_str;
            }
            $i++;
        }
        $friends_array = array(
            'assoc' => $friends_array_assoc,
            'normal' => $friends_array_normal
        );
        return $friends_array;
    }
    
}