<?php
/**
 *
 * Data-Portraits/model/class.ConnectionProcessing.php
 * Model for processing Connections object
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
require_once(ROOT_PATH."/model/class.Connection.php");
require_once(ROOT_PATH."/model/class.UserProcessing.php");
require_once(ROOT_PATH."/model/class.StatusProcessing.php");

class ConnectionProcessing {
    
    private $connection;
    private $vals;
    private $mutuals;
    
    public function __construct($connection, $vals, $mutuals) {
        $this->connection = $connection;
        $this->vals = $vals;
        if (!$mutuals) {
            $friends_data = $this->getAllFriends($connection, $vals);
            $followers_data = $this->getAllFollowers($connection, $vals);
            $this->mutuals = $this->getAllMutuals($friends_data['assoc'], $followers_data['assoc']);
            unset($friends_data);
            unset($followers_data);
        } else {
            $this->mutuals = $mutuals;
        }
    }
    
    public function getFollowers() {
        $vals = $this->vals;
        $user = $vals['screen_name'];
        $content = $this->connection->search("@".$user);
        $retweets = array();
        $mentions = array();
        foreach ($content->results as $result) {
            $status = new Status($result);
            if (strstr($status->text, "RT @".$user)) {
                array_push($retweets, $status);                
            } else {
                array_push($mentions, $status);
            }
        }
        $data = array();
        foreach ($retweets as $retweet) {
            if (isset($data[$retweet->created_by_id]['retweets_me_count'])) {
                $data[$retweet->created_by_id]['retweets_me_count']++;
                array_push($data[$retweet->created_by_id]['retweets'], $retweet);
            } else {
                $data[$retweet->created_by_id]['retweets_me_count'] = 1;
                $data[$retweet->created_by_id]['retweets'] = array();
                array_push($data[$retweet->created_by_id]['retweets'], $retweet);
            }
        }
        
        foreach ($mentions as $mention) {
            if (isset($data[$mention->created_by_id]['mentions_me_count'])) {
                $data[$mention->created_by_id]['mentions_me_count']++;
                array_push($data[$mention->created_by_id]['mentions'], $mention);
            } else {
                $data[$mention->created_by_id]['mentions_me_count'] = 1;
                $data[$mention->created_by_id]['mentions'] = array();
                array_push($data[$mention->created_by_id]['mentions'], $mention);
            }
        }
        
        $total_count = count($data);
        $rand_max = getrandmax();
        $total = 95;
        $temp = $this->connection->getFollowers($vals);
        $followers = $temp->ids;
        $count_fols = count($followers);
        $max_base = $count_fols<$rand_max?$count_fols:$rand_max;
        $base = rand(1, $max_base);
        $factor = rand(1, round($max_base/10));
        $remaining = $total - $total_count;
        $index = $base;
        $flag = 0;
        unset($temp);
        for ($i = 0; $i < $remaining; $i++) {
            $index = ($index + $i*$factor)%$count_fols;
            $collisions = 0;
            while (isset($data[$followers[$index]])) {
                $index = ($index+1)%$count_fols;
                $collisions++;
                if ($collisions == 10) {
                    $flag = 1;
                    break;
                }
            }
            if ($flag) {
                break;
            }
            $data[$followers[$index]]['retweets_me_count'] = 0;
            $data[$followers[$index]]['retweets'] = array();
        }
        
        $data = $this->processData($data);
        $array = array(
            'final_list' => $data,
            'mutuals' => $this->mutuals
        );
        return $array;
    }
    
    public function getFollowees($statuses) {
        $vals = $this->vals;
        $user = $vals['screen_name'];
        $data = array();
        $retweets_of = array();
        // Find mentions by main user
        foreach ($statuses as $status) {
            if (isset($status->retweet_count) && $status->retweet_count) {
                array_push($retweets_of, $status->id);
            }
            if ($status->mentions) {
                foreach ($status->mentions as $mention) {
                    if ($mention != $user) {
                        if (isset($data[$mention]['mentions_by_me_count'])) {
                            $data[$mention]['mentions_by_me_count']++;
                            array_push($data[$mention]['mentions'],$status);
                        } else {
                            $data[$mention]['mentions_by_me_count'] = 1;
                            $data[$mention]['mentions'] = array();
                            array_push($data[$mention]['mentions'], $status);
                        }
                    }
                }
            }
        }
        
        // Find Retweets of Main User
        $retweets_by = StatusProcessing::getRetweetsByUser($this->connection, $vals);
        foreach ($retweets_by as $retweet) {
            if ($retweet->retweet_of) {
                $status = $retweet->retweet_of;
                if (isset($data[$status->created_by_id]['retweets_by_me_count'])) {
                    $data[$status->created_by_id]['retweets_by_me_count']++;
                    array_push($data[$status->created_by_id]['retweets'], $status);
                } else {
                    $data[$status->created_by_id]['retweets_by_me_count'] = 1;
                    $data[$status->created_by_id]['retweets'] = array();
                    array_push($data[$status->created_by_id]['retweets'], $status);
                }
            }
        }
        
        $total_count = count($data);
        $rand_max = getrandmax();
        $total = 95;
        $temp = $this->connection->getFriends($vals);
        $friends = $temp->ids;
        $count_frnds = count($friends);
        $max_base = $count_frnds<$rand_max?$count_frnds:$rand_max;
        $base = rand(1, $max_base);
        $factor = rand(1, round($max_base/10));
        $remaining = $total - $total_count;
        $index = $base;
        $flag = 0;
        unset($temp);
        for ($i = 0; $i < $remaining; $i++) {
            $index = ($index + $i*$factor)%$count_frnds;
            $collisions = 0;
            while (isset($data[$friends[$index]])) {
                $index = ($index+1)%$count_frnds;
                $collisions++;
                if ($collisions == 10) {
                    $flag = 1;
                    break;
                }
            }
            if ($flag) {
                break;
            }
            $data[$friends[$index]]['retweets_me_count'] = 0;
            $data[$friends[$index]]['retweets'] = array();
        }
        
        $data = $this->processData($data);
        $array = array(
            'final_list' => $data,
            'mutuals' => $this->mutuals
        );
        return $array;
    }
    
    public function processData($data) {
        $users = array();
        foreach ($data as $user => $item) {
            array_push($users, $user);
        }
        
        $users_details = UserProcessing::getUsersDetails($this->connection, $users);
        $count = count($users);
        
        $final_list = array();
        
        for ($i = 0; $i < $count; $i++) {
            if (isset($data[$users_details[$i]->id]['retweets_me_count']) || isset($data[$users_details[$i]->id]['mentions_me_count'])) {
                $relation = "follower";
            } else if (isset($data[$users_details[$i]->id]['retweets_by_me_count']) || isset($data[$users_details[$i]->id]['mentions_by_me_count'])){
                $relation = "friend";
            } else {
                continue;
            }
            if (in_array($users_details[$i]->id, $this->mutuals)) {
                $relation = "mutual";
            }
            $array = array(
                'user' => $users_details[$i],
                'relation' => $relation
            );
            if (isset($data[$users_details[$i]->id]['mentions_by_me_count'])) {
                $array['mentions_by_me_count'] = $data[$users_details[$i]->id]['mentions_by_me_count'];
                $array['mentions'] = $data[$users_details[$i]->id]['mentions'];
            }
            if (isset($data[$users_details[$i]->id]['retweets_by_me_count'])) {
                $array['retweets_by_me_count'] = $data[$users_details[$i]->id]['retweets_by_me_count'];
                $array['retweets'] = $data[$users_details[$i]->id]['retweets'];
            }
            
            $friendship = new Connection($array);
            $final_list[$friendship->user->username] = $friendship;
        }
        
        $final_list = self::normalizeWeights($final_list);
        return $final_list;
    }
    
    public static function normalizeWeights($connections) {
        $max = 0;
        foreach ($connections as $connection) {
            if ($connection->weight > $max) {
                $max = $connection->weight;
            }
        }
        if ($max) {
            foreach ($connections as $connection) {
                $connection->weight /= $max;
            }
        }
        return $connections;
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
    
    private function getAllFollowers() {
        $vals = $this->vals;
        $vals['cursor'] = -1;
        $followers_array_assoc = array();
        $followers_array_normal = array();
        $i = 0;
        while (1) {
            if ($i >= 11) {
                break;
            }
            $followers = $this->connection->getFollowers($vals);
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
    
    private function getAllFriends() {
        $vals = $this->vals;
        $vals['cursor'] = -1;
        $friends_array_assoc = array();
        $friends_array_normal = array();
        $i = 0;
        while (1) {
            if ($i >= 11) {
                break;
            }
            $friends = $this->connection->getFriends($vals);
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