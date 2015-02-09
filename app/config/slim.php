<?php

$config['slim'] = array(
    // Modular
    'modular'       => true,

    // Application
    'mode'          => 'development',

    // Debugging
    'debug'         => true,

    // Logging
    'log.writer'    => new \Slim\Logger\DateTimeFileWriter(array("path"=> __DIR__ . "/../../../logs")),
    'log.level'     => \Slim\Log::DEBUG,
    'log.enabled'   => true,

    //View
    'view'          => new \Slim\Views\Twig(),
    'templates.path'=> APP_PATH.'views',

    // HTTP
    'http.version' => '1.1',

    // Routing
    'routes.case_sensitive' => true,

    //logging
    //'log.level' => \Slim\Log::INFO,
    //'log.enabled' => true,
    //'log.writer' => new \Slim\Logger\DateTimeFileWriter()


    //custom logging
    //'log.writer' => new MyLogWriter()

);