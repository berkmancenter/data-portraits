<?php
require_once("crawler/class.Crawler.php");

$crawler = new Crawler("ginatrapani");
$crawler->getFriends();
echo "\n\n";
echo $crawler->getFollowers();