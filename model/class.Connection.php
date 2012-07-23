<?php
/**
 *
 * Data-Portraits/model/class.Connection.php
 * Class defining Connection Object.
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

class Connection {
    
    /**
     * @var object
     */
    var $user;
    /**
     * @var str
     */
    var $relation;
    /**
     * Number of times the user is mentioned by the main user
     * @var int
     */
    var $mentions_by_me_count;
    /**
     * Number of times the user mentions the main user.
     * @var int
     */
    var $mentions_me_count;
    /**
     * Array of tweets containing both types of mentions
     * @var array
     */
    var $mentions;
    /**
     * Number of times the user is retweeted by the main user.
     * @var int
     */
    var $retweets_by_me_count;
    /**
     * Number of times the user retweets the main user.
     * @var int
     */
    var $retweets_me_count;
    /**
     * Array of tweets containing both types of retweets
     * @var array
     */
    var $retweets;
    
    var $weight;
    
    public function __construct($vals) {
        $this->user = $vals['user'];
        $this->relation = $vals['relation'];
        if (isset($vals['mentions_by_me_count'])) {
            $this->mentions_by_me_count = $vals['mentions_by_me_count'];
        }
        if (isset($vals['mentions_me_count'])) {
            $this->mentions_me_count = $vals['mentions_me_count'];
        }
        if (isset($vals['mentions'])) {
            $this->mentions = $vals['mentions'];
        }
        if (isset($vals['retweets_by_me_count'])) {
            $this->retweets_by_me_count = $vals['retweets_by_me_count'];
        }
        if (isset($vals['retweets_me_count'])) {
            $this->retweets_me_count = $vals['retweets_me_count'];
        }
        if (isset($vals['retweets'])) {
            $this->retweets = $vals['retweets'];
        }
        $this->weight = $this->calculateWeight();
    }
    
    public function calculateWeight() {
        $by_me_actions_count = $this->mentions_by_me_count*1.5 + $this->retweets_by_me_count;
        $on_me_actions_count = $this->mentions_me_count*1.5 + $this->retweets_me_count;
        $weight = 1.2*$by_me_actions_count + $on_me_actions_count;
        switch($this->relation) {
            case "follower":
                break;
            case "friend":
                $weight = 1.1*$weight;
                break;
            case "mutual":
                $weight = 1.2*$weight;
                break;
        }
        return $weight;
    }
}