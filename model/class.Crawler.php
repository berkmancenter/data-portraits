<?php
/**
 *
 * Data-Portraits/model/class.Crawler.php
 * Model for actual crawling of Twitter
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

class Crawler {
    
    /**
     * @var object
     */
    private $connection;
    
    /**
     * Constructor function. Creates new TwitterOAuth class
     * @param $oauth array
     */
    public function __construct($oauth) {
        $this->connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_KEY_SECRET,
                                             $oauth['token'],
                                             $oauth['token_secret']);
    }
    
    // SEARCH
    public function search($search_string) {
        $vals['q'] = $search_string;
        $vals['include_entities'] = true;
        $vals['lang'] = "en";
        $vals['rpp'] = 100;
        $content = $this->connection->get("search", $vals);
        return $content;
    }
    
    // TIMELINES
      
    /**
     * Returns the most recent statuses, including retweets if they exist,
     * posted by the authenticating user and the users they follow.
     * @param $vals array
     */
    public function homeTimeline($vals = NULL) {
        $content = $this->connection->get("statuses/home_timeline", $vals);
        return $content;
    }
    
    /**
     * Returns the 20 most recent mentions (status containing @username)
     * for the authenticating user.
     * @param $vals array
     */
    public function getMentions($vals = NULL) {
        $content = $this->connection->get('statuses/mentions', $vals);
        return $content;
    }
    
    /**
     * Returns the 20 most recent retweets posted by the authenticating user.
     * @param $vals array
     */
    public function retweetsToMe($vals = NULL) {
        $content = $this->connection->get('statuses/retweeted_to_me', $vals);
        return $content;
    }
    
    /**
     * Returns the 20 most recent retweets posted by users the authenticating
     * user follow.
     * @param $vals array
     */
    public function retweetsByMe($vals = NULL) {
        $content = $this->connection->get('statuses/retweeted_by_me', $vals);
        return $content;
    }
    
    /**
     * Returns the 20 most recent tweets of the authenticated user that
     * have been retweeted by others.
     * @param $vals array
     */
    public function retweetsOfMe($vals = NULL) {
        $content = $this->connection->get('statuses/retweets_of_me', $vals);
        return $content;
    }
    
    /**
     * Returns the 20 most recent statuses posted by the specified user.
     * Mandatory index $vals['screen_name'] or $vals['user_id']
     * @param $vals array
     */
    public function userTimeline($vals) {
        $content = $this->connection->get('statuses/user_timeline', $vals);
        return $content;
    }
    
    /**
     * Returns the 20 most recent retweets posted by users the specified user
     * follows.
     * Mandatory index $vals['screen_name'] or $vals['user_id']
     * @param $vals array
     */    
    public function retweetsToUser($vals) {
        $content = $this->connection->get('statuses/retweeted_to_user', $vals);
        return $content;
    }
    
    /**
     * Returns the 20 most recent retweets posted by the specified user.
     * Mandatory index $vals['screen_name'] or $vals['user_id']
     * @param $vals array
     */
    public function retweetsByUser($vals) {
        $content = $this->connection->get('statuses/retweeted_by_user', $vals);
        return $content;
    }
    
    // TWEETS
    
    /**
     * Show user objects of up to 100 members who retweeted the status.
     * @param $id str
     * @param $vals array
     */
    public function statusRetweetedBy($id, $vals = NULL) {
        $str = 'statuses/'.$id.'/retweeted_by';
        $content = $this->connection->get($str, $vals);
        return $content;
    }
    
    /**
     * Show user ids of up to 100 users who retweeted the status.
     * @param $id str
     * @param $vals array
     */
    public function statusRetweetedById($id, $vals = NULL) {
        $str = 'statuses/'.$id.'/retweeted_by/ids';
        $content = $this->connection->get($str, $vals);
        return $content;
    }
    
    /**
     * Returns up to 100 of the first retweets of a given tweet.
     * @param $id str
     * @param $vals array
     */
    public function getRetweets($id, $vals = NULL) {
        $str = 'statuses/retweets/'.$id;
        $content = $this->connection->get($str, $vals);
        return $content;
    }
    
    // FRIENDS AND FOLLOWERS
    
    /**
     * Returns an array of numeric IDs for every user following the specified
     * user.
     * Mandatory index $vals['screen_name'] or $vals['user_id']
     * @param $vals array
     */
    public function getFollowers($vals) {
        $content = $this->connection->get('followers/ids', $vals);
        return $content;
    }
    
    /**
     * Returns an array of numeric IDs for every user the specified user is
     * following.
     * Mandatory index $vals['screen_name'] or $vals['user_id']
     * @param $vals array
     */
    public function getFriends($vals) {
        $content = $this->connection->get('friends/ids', $vals);
        return $content;
    }
    
    /**
     * Test for the existence of friendship between two users.
     * Mandatory index $vals['screen_name_a'] or $vals['user_id_a']
     * Mandatory index $vals['screen_name_b'] or $vals['user_id_b']
     * @param $vals array
     */
    public function friendshipExists($vals) {
        $content = $this->connection->get('friendships/exists', $vals);
        return $content;
    }
    
    /**
     * Returns detailed information about the relationship between two users
     * Mandatory index $vals['source_id'] or $vals['source_screen_name']
     * Mandatory index $vals['target_id'] or $vals['target_screen_name']
     * @param $vals array
     */
    public function showFriendships($vals) {
        $content = $this->connection->get('friendships/show', $vals);
        return $content;
    }
    
    // USERS
    
    /**
     * Return up to 100 users worth of extended information, specified by
     * either ID, screen name, or combination of the two.
     * Mandatory index $vals['screen_name'] or $vals['user_id']
     * @param $vals array
     */
    public function userDetails($vals) {
        $content = $this->connection->get('users/lookup', $vals);
        return $content;
    }
    
    /**
     * Return up to 100 users worth of extended information, specified by
     * either ID, screen name, or combination of the two.
     * Mandatory index $vals['screen_name'] or $vals['user_id']
     * @param $vals array
     */
    public function multipleUsersDetails($vals) {
        $content = $this->connection->post('users/lookup', $vals);
        return $content;
    }
    
    /**
     * Returns extended information of a given user
     * Mandatory index $vals['screen_name'] or $vals['user_id']
     * @param $vals array
     */
    public function extendedUserDetails($vals) {
        $content = $this->connection->get('users/show', $vals);
        return $content;
    }
    
    // FAVORITES
    
    /**
     * Returns the 20 most recent favorite statuses for the authenticating
     * or specified user
     * @param $vals array
     */
    public function getFavorites($vals = NULL) {
        $content = $this->connection->get('favorites', $vals);
        return $content;
    }
    
    // LISTS
    
    /**
     * Returns all lists the authenticating or specified user subscribes to,
     * including their own
     * @param $vals array
     */
    public function getLists($vals = NULL) {
        $content = $this->connection->get('lists/all', $vals);
        return $content;
    }
    
    /**
     * Returns tweet timeline for members of the specified list.
     * Mandatory index $vals['list_id']
     * @param $vals array
     */
    public function getListStatuses($vals) {
        $content = $this->connection->get('lists/statuses', $vals);
        return $content;
    }
    
    /**
     * Returns the lists the specified user has been added to.  If user_id or
     * screen_name are not provided the memberships for the authenticating
     * user are returned.
     * @param $vals array
     */
    public function getListMemberships($vals = NULL) {
        $content = $this->connection->get('lists/memberships', $vals);
        return $content;
    }
    
    /**
     * Returns the subscribers of the specified list
     * Mandatory index $vals['list_id']
     * @param $vals array
     */
    public function getListSubscribers($vals) {
        $content = $this->connection->get('lists/subscribers', $vals);
        return $content;
    }
    
    /**
     * Returns the members of the specified list.
     * Mandatory index $vals['list_id']
     * @param $vals array
     */
    public function getListMembers($vals) {
        $content = $this->connection->get('lists/members', $vals);
        return $content;
    }
    
    /**
     * Returns the lists of the specified (or authenticated) user.
     * @param $vals array
     */
    public function lists($vals = NULL) {
        $content = $this->connection->get('lists', $vals);
        return $content;
    }
    
    /**
     * Obtain a collection of the lists the specified user is subscribed to
     * Mandatory index $vals['screen_name'] or $vals['user_id']
     * @param $vals array
     */
    public function getSubscriptions($vals) {
        $content = $this->connection->get('lists/subscriptions', $vals);
        return $content;
    }
}