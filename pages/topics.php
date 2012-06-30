<?php
chdir("..");
require_once("init.php");
require_once(ROOT_PATH."/controller/class.TopicModellingController.php");

$controller = new TopicModellingController();
echo $controller->go();