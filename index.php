<?php
require_once("init.php");
require_once(ROOT_PATH."/controller/class.LandingPageController.php");

$controller = new LandingPageController();
echo $controller->go();