<?php
require_once("../init.php");
require_once("../controller/class.TwitterCrawlerController.php");

$controller = new TwitterCrawlerController();
echo $controller->go();