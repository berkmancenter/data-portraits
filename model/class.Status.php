<?php
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
     * Constructor
     * @param array $val User key/value pairs
     * @return User New user
     */
    public function __construct($val = false) {
        if($val) {
            $this->id = $val->id_str;
            $this->created = $val->created_at;
            $this->text = $val->text;
            $this->text_processed = $val->text;
            $this->source = $val->source;
            $this->in_reply_to_status = $val->in_reply_to_status_id_str;
            $this->in_reply_to_user = $val->in_reply_to_screen_name;
            $this->coordinates = $val->coordinates;
            $this->place = $val->place;
            $this->retweet_count = $val->retweet_count;
            $this->created_by = $val->user->screen_name;
            $this->hashtags = self::processHashTags($val, $this->text_processed);
            $this->mentions = self::processUserMentions($val, $this->text_processed);
            $this->urls = self::processURLs($val, $this->text_processed);
            $this->emoticons = self::processEmoticons($val, $this->text_processed);
            $this->text_processed = strtolower(Utils::preprocessTweet($this->text_processed));
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
        if (!count($tweet->entities->urls)) {
            return false;
        }
        $urls = array();
        foreach ($tweet->entities->urls as $entity) {
            array_push($urls, $entity->expanded_url);
            // Process the tweet by removing the mention
            $length = $entity->indices[1]-$entity->indices[0];
            $empty_string = null;
            for ($i=0; $i<$length; $i++) $empty_string .= " ";
            $text_processed = substr_replace($text_processed,
                                        $empty_string,
                                        $entity->indices[0],$length);
        }
        return $urls;
    }
    
    private static function processEmoticons($tweet, &$text_processed) {
        $text_processed = str_replace('&lt;','<', $text_processed);
        $text_processed = str_replace('&gt;','>', $text_processed);
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
}