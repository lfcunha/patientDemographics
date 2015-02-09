<?php

session_start();



date_default_timezone_set("America/New_York");
$app = require './app/bootstrap/start.php';   #returns $starter, which is a boostrap object, which has a run() function that calls $app->run()
$app->run();






//https://github.com/codeguy/Slim-Extras
//http://www.phptherightway.com/














