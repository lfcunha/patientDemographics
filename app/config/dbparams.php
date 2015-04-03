<?php

// MySQL host name, user name, password, database
// This part is for phpMyEdit

require_once "/etc/authentication/samplesdb.php";

$opts['hn'] = HOST;
$opts['un'] = USERNAME;
$opts['pw'] = PASSWORD;
$opts['db'] = DATABASE;
$opts['salt']= SALT;
$opts['cookieSecret'] = COOKIESECRET;
