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
    var $username;
    /**
     *
     * @var str
     */
    var $full_name;
    /**
     *
     * @var str
     */
    var $avatar;
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
    var $follower_count;
    /**
     *
     * @var int
     */
    var $friend_count;
    /**
     *
     * @var int
     */
    var $favorites_count;
    /**
     *
     * @var int
     */
    var $post_count;
    /**
     *
     * @var str
     */
    var $found_in;
    /**
     *
     * @var int
     */
    var $last_post;
    /**
     *
     * @var date
     */
    var $joined;
    /**
     *
     * @var int
     */
    var $last_post_id;
    /**
     *
     * @var int
     */
    var $user_id;
    /**
     *
     * @var array
     */
    var $other = array();
    
}