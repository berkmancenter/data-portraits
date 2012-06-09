<?php
chdir("..");
require_once("init.php");
require_once(ROOT_PATH."/controller/class.HomePageController.php");

$controller = new HomePageController();
echo $controller->go();