<?php
require_once("crawler/model/class.Crawler.php");

$crawler = new Crawler("ginatrapani");
//print_r($crawler->getFriends());
//print_r($crawler->getFollowers());
print_r($crawler->userShowAPI());