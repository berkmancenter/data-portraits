<?php
chdir("..");
require_once("init.php");
require_once(ROOT_PATH."/controller/class.SentimentAnalysisController.php");

$controller = new SentimentAnalysisController();
echo $controller->go();