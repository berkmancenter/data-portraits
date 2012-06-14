<?php
chdir("..");
require_once("init.php");
require_once("controller/class.TwitterLoginController.php");

$controller = new TwitterLoginController();
echo $controller->go();