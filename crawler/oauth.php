<?php
chdir("..");
require_once("init.php");
require_once("controller/class.TwitterOAuthController.php");

$controller = new TwitterOAuthController();
echo $controller->go();