<?php
/**
 *
 * Data-Portraits/controller/class.ConnectionsController.php
 * Class for creating the connections page
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
require_once(ROOT_PATH."/controller/class.DPController.php");
require_once(ROOT_PATH."/model/class.Crawler.php");
require_once(ROOT_PATH."/model/class.ConnectionProcessing.php");

class ConnectionsController extends DPController {
    
    public function go() {
        $this->setViewTemplate("connections.tpl");
        $this->addToView('connections', null);
        if (isset($_POST['username'])) {
            $statuses = json_decode($_POST['statuses']);
            $people = self::crawl($_POST['username'], $statuses);
            $connections = "var connections = ";
            $connections .= json_encode($people);
            unset($people);
            $this->addToView('connections', $connections);
            //$this->addToView('connections', $people);
        }
        $this->generateView();
    }
    
    private function crawl($username, $statuses) {
        $authentication = array(
            'token' => $_SESSION['oauth_token'],
            'token_secret' => $_SESSION['oauth_secret']
        );
        
        $vals = array(
            'screen_name' => $username
        );
        $connection = new Crawler($authentication);
        $connection_processing = new ConnectionProcessing($connection, $vals);
        $people = $connection_processing->getFollowees($statuses);
        //$json = self::convertToJSON($username, $people);
        //echo $json;
        return $people;
    }
    
    private function convertToJSON($username, $people) {
        $all_nodes = array();
        $adjacencies_friends = array();
        foreach ($people as $user) {
            if ($user->relation == "friend") {
                $node_data = array(
                    '$color' => "#FF0000",
                    '$type' => "triangle",
                    '$dim' => 10*$user->weight
                );
                $edge_data = array("color" => "#FF0000");
            } else if ($user->relation == "mutual") {
                $node_data = array(
                    '$color' => "#0000FF",
                    '$type' => "square",
                    '$dim' => 10*$user->weight
                );
                $edge_data = array("color" => "#0000FF");
            } else {
                $node_data = array(
                    '$color' => "#00FF00",
                    '$type' => "circle",
                    '$dim' => 10*$user->weight
                );
                $edge_data = array("color" => "#00FF00");
            }
            $connection = array(
                "nodeTo" => $user->user->username,
                "nodeFrom" => $username,
                "data" => $edge_data
            );
            array_push($adjacencies_friends, $connection);
            $node = array(
                "adjacencies" => array(),
                "data" => $node_data,
                "id" => $user->user->username,
                "name" => $user->user->username,
                "location" => $user->user->location,
                "desc" => $user->user->description,
                "statuses" => $user->user->statuses_count,
                "avatar" => $user->user->avatar
            );
            array_push($all_nodes, $node);            
        }
        $main_user_data = array(
            '$color' => "yellow",
            '$type' => "star",
            '$dim' => 10
        );
        $main_user = array(
            "adjacencies" => $adjacencies_friends,
            "data" => $main_user_data,
            "id" => $username,
            "name" => $username,
            "location" => $user->user->location,
            "desc" => $user->user->description,
            "statuses" => $user->user->statuses_count,
            "avatar" => $user->user->avatar
        );
        array_push($all_nodes, $main_user);
        return "var json = ".json_encode($all_nodes).";";
    }
    
}