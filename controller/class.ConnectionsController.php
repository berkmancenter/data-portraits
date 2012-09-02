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
        if (isset($_POST['type']) && $_POST["type"] == "friend") {
            $this->setViewTemplate("friends.tpl");
        } else {
            $this->setViewTemplate("followers.tpl");
        }
        if (isset($_POST['username'])) {
            $statuses = json_decode($_POST['statuses']);
            $people = self::crawl($_POST['username'], $statuses);
            $connections = "var connections = ";
            $connections .= json_encode($people['final_list']);
            $mutuals = "var mutuals = ";
            $mutuals .= json_encode($people['mutuals']);
            unset($people);
            $this->addToView('connections', $connections);
            $this->addToView('mutuals', $mutuals);
        }
        $type = "var type = '".$_POST['type']."'";
        $this->addToView('type', $type);
        $this->generateView();
    }
    
    private function compare($a, $b) {
        return $a->weight < $b->weight;
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
        if (isset($_POST['mutuals'])) {
            $connection_processing = new ConnectionProcessing($connection, $vals, $_POST['mutuals']);
        } else {
            $connection_processing = new ConnectionProcessing($connection, $vals, null);
        }
        if (isset($_POST['type']) && $_POST["type"] == "friend") {
            $people = $connection_processing->getFollowees($statuses);
        } else {
            $people = $connection_processing->getFollowers();
        }
        uasort($people['final_list'], array($this, 'compare'));
        return $people;
    }
}