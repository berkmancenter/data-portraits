<?php
/**
 *
 * Data-Portraits/model/class.User.php
 * Class defining User object
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

class User {
    
    /**
     *
     * @var int
     */
    var $id;
    /**
     *
     * @var str
     */
    var $avatar;
    /**
     *
     * @var str
     */
    var $username;
    /**
     *
     * @var str
     */
    var $full_name;
    /**
     *
     * @var location
     */
    var $location;
    /**
     *
     * @var description
     */
    var $description;
    /**
     *
     * @var url
     */
    var $url;
    /**
     *
     * @var bool
     */
    var $is_protected;
    /**
     *
     * @var int
     */
    var $followers_count;
    /**
     *
     * @var int
     */
    var $friends_count;
    /**
     *
     * @var int
     */
    var $favorites_count;
    /**
     *
     * @var int
     */
    var $statuses_count;
    /**
     *
     * @var int
     */
    var $listed_count;
    /**
     *
     * @var date
     */
    var $joined;
    
    /**
     * Constructor
     * @param array $val User key/value pairs
     * @return User New user
     */
    public function __construct($val = false) {
        if($val) {
            $this->id = $val->id_str;
            $this->avatar = $val->profile_image_url;
            $this->username = $val->screen_name;
            if (isset($val->name)) {
                $this->full_name = $val->name;
            }
            if (isset($val->location)) {
                $this->location = $val->location;
            }
            if (isset($val->description) && $val->description!="") {
                $this->description = $val->description;
            } else {
                $this->description = false;
            }
            if (isset($val->url) && $val->url!="") {
                $this->url = $val->url;
            } else {
                $this->url = false;
            }
            $this->is_protected = $val->protected;
            $this->followers_count = $val->followers_count;
            $this->friends_count = $val->friends_count;
            $this->favorites_count = $val->favourites_count;
            $this->statuses_count = $val->statuses_count;
            $this->listed_count = $val->listed_count;
            $this->joined = $val->created_at;
        }
    }
    
}