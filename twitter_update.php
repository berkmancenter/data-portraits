<?php
require_once("extlib/twitteroauth/twitteroauth/twitteroauth.php");
session_start();

//print_r($_SESSION);
//echo (!empty($_SESSION['username']) ? '@' . $_SESSION['username'] : 'Guest');

function getConnectionWithAccessToken($oauth_token, $oauth_token_secret) {
  $connection = new TwitterOAuth('yGxmTdRZzJe6A14QZMA',
                                 'YqDtaiYnU1O6irng5AUbQJWymcEu8Hb9k75CQJAZM',
                                 $oauth_token,
                                 $oauth_token_secret);
  return $connection;
}

$token_r = $_SESSION['oauth_token'];
$secret_r = $_SESSION['oauth_secret'];

$connection = getConnectionWithAccessToken($token_r,                                            $secret_r);
$content = $connection->get("statuses/home_timeline");

$nettuts_timeline = $connection->get('statuses/user_timeline',
                                     array('screen_name' => 'nettuts'));

print_r($nettuts_timeline);

?>