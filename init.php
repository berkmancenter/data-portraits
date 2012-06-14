<?php
require_once("extlib/twitteroauth/twitteroauth.php");
require_once("config.inc.php");
require_once("model/class.Loader.php");
require_once("model/class.Database.php");

session_start();
Loader::defineConstants();
$db = new Database();