<?php
chdir("..");
require_once("init.php");
require_once(ROOT_PATH."/controller/class.WordAnalysisController.php");

$controller = new WordAnalysisController();
echo $controller->go();