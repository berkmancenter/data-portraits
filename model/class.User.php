<?php

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