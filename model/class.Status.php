<?php
/**
 *
 * Data-Portraits/model/class.Status.php
 * Class defining Status Object.
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
require_once(ROOT_PATH."/model/class.Utils.php");

class Status {
    
    /**
     *
     * @var int
     */
    var $id;
    /**
     *
     * @var date
     */
    var $created;
    /**
     *
     * @var str
     */
    var $text;
    /**
     *
     * @var str
     */
    var $text_processed;   
    /**
     *
     * @var str
     */
    var $source;
    /**
     *
     * @var str
     */
    var $in_reply_to_status;
    /**
     *
     * @var str
     */
    var $in_reply_to_user;
    /**
     * @var object
     */
    var $coordinates;
    /**
     *
     * @var object
     */
    var $place;
    /**
     *
     * @var int
     */
    var $retweet_count;
    /**
     *
     * @var str
     */
    var $created_by;
    /**
     *
     * @var array
     */
    var $hashtags;
    /**
     *
     * @var array
     */
    var $mentions;
    /**
     *
     * @var array
     */
    var $urls;
    /**
     *
     * @var array
     */
    var $emoticons;
    /**
     *
     * @var object
     */
    var $retweet_of;
    
    /**
     * Constructor
     * @param array $val User key/value pairs
     * @return User New user
     */
    public function __construct($val = false) {
        if($val) {
            $this->id = $val->id_str;
            $this->created = $val->created_at;
            $this->text = self::removeHTMLEntities($val->text);
            $this->text_processed = self::removeHTMLEntities($val->text);
            
            $this->in_reply_to_status = isset($val->in_reply_to_status_id_str)?$val->in_reply_to_status_id_str:null;
            $this->in_reply_to_user = isset($val->in_reply_to_screen_name)?$val->in_reply_to_screen_name:null;
            $this->coordinates = isset($val->coordinates)?$val->coordinates:null;
            $this->place = isset($val->place)?$val->place:null;
            $this->retweet_count = isset($val->retweet_count)?$val->retweet_count:null;
            if (isset($val->user->screen_name) || isset($val->from_user)) {
                if (isset($val->user->screen_name)) {
                    $this->created_by = $val->user->screen_name;
                } else {
                    $this->created_by = $val->from_user;
                }
            }
            $this->hashtags = self::processHashTags($val, $this->text_processed);
            $this->mentions = self::processUserMentions($val, $this->text_processed);
            $this->urls = self::processURLs($val, $this->text_processed);
            $this->emoticons = self::processEmoticons($this->text_processed);
            $this->text_processed = strtolower(Utils::preprocessTweet($this->text_processed));
            if (isset($val->retweeted_status)) {
                $this->retweet_of = new self($val->retweeted_status);
            } else {
                $this->retweet_of = null;
            }
        }
    }
    
    private static function processHashTags($tweet, &$text_processed) {
        if (!count($tweet->entities->hashtags)) {
            return false;
        }
        $hashtags = array();
        foreach ($tweet->entities->hashtags as $entity) {
            array_push($hashtags, $entity->text);
            
            // Process the tweet by removing the mention
            $length = $entity->indices[1]-$entity->indices[0];
            $empty_string = null;
            for ($i=0; $i<$length; $i++) $empty_string .= " ";
            $text_processed = substr_replace($text_processed,
                                        $empty_string,
                                        $entity->indices[0],$length);
        }
        return $hashtags;
    }
    
    private static function processUserMentions($tweet, &$text_processed) {
        if (!count($tweet->entities->user_mentions)) {
            return false;
        }
        $mentions = array();
        foreach ($tweet->entities->user_mentions as $entity) {
            array_push($mentions, $entity->screen_name);
            
            // Process the tweet by removing the mention
            $length = $entity->indices[1]-$entity->indices[0];
            $empty_string = null;
            for ($i=0; $i<$length; $i++) $empty_string .= " ";
            $text_processed = substr_replace($text_processed,
                                        $empty_string,
                                        $entity->indices[0],$length);
        }
        return $mentions;
    }
    
    private static function processURLs($tweet, &$text_processed) {
        $urls = array();
        if (count($tweet->entities->urls)) {
            foreach ($tweet->entities->urls as $entity) {
                array_push($urls, self::removeAmpersand($entity->expanded_url));
                // Process the tweet by removing the mention
                $length = $entity->indices[1]-$entity->indices[0];
                $empty_string = null;
                for ($i=0; $i<$length; $i++) $empty_string .= " ";
                $text_processed = substr_replace($text_processed,
                                            $empty_string,
                                            $entity->indices[0],$length);
            }
        }
        $text_processed = self::detectRemainingURLs($text_processed, $urls);
        if (count($urls) == 0) {
            return false;
        }
        return $urls;
    }
    
    private static function processEmoticons(&$text_processed) {
        $text = strtoupper($text_processed);
        $emoticons = array(
            ":)", ":-)", ":]", "=)", ":-(", ":(", ":[", "=(", ":-P", ":P",
            "=P", ":-D", ":D", "=D", ":-O", ":O", ";-)", ";)", ":-/", ":/",
            ":'(", ":-*", ":*", "^_^", "<3", "-_-", "O.O"
        );
        $smileys = array();
        foreach ($emoticons as $emoticon) {
            $pos = strpos($text, $emoticon);
            while (!($pos === false))  {
                array_push($smileys, $emoticon);
                if (($len = strlen($emoticon)) == 2) {
                    $replace = "  ";
                } else {
                    $replace = "   ";
                }
                $text_processed = substr_replace($text_processed, $replace,
                                                 $pos, $len);
                $text = substr_replace($text, $replace, $pos, $len);
                $pos = strpos($text, $emoticon);
            }
        }
        if (!count($smileys)) {
            return false;
        }
        return $smileys;
    }
    
    private static function removeHTMLEntities($text) {
        $text = str_replace('&lt;','<', $text);
        $text = str_replace('&gt;','>', $text);
        $text = str_replace('&amp;','&', $text);
        $text = str_replace('&','and', $text);
        $text = str_replace('&quot;','"', $text);
        $text = str_replace('&#39;',"'", $text);
        return $text;
    }
    
    private static function removeAmpersand($text) {
        $text = str_replace('&amp;', '&', $text);
        return str_replace('&', '[AND]', $text);
    }
    
    private static function detectRemainingURLs($text, &$urls) {
        $words = explode(" ", $text);
        $new_text = "";
        $reg_exUrl = "/^(http|https|ftp|ftps|www)/";
        foreach ($words as $word) {
            if(preg_match($reg_exUrl, $word)) {
                array_push($urls, $word);
                continue;
            }
            $new_text .= $word." ";
        }
        return $new_text;
    }
}