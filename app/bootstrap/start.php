<?php
/**
 * Created by PhpStorm.
 * User: luiscunha
 * Date: 7/2/14
 * Time: 12:15 AM
 */

define('ROOT_PATH'  , __DIR__.'/../../');
define('VENDOR_PATH', __DIR__.'/../../vendor/');
define('APP_PATH'   , __DIR__.'/../../app/');
define('MODULE_PATH', __DIR__.'/../../app/modules/');
define('PUBLIC_PATH', __DIR__.'/../../public/');


//require VENDOR_PATH . 'slim/slim/Slim/Slim.php';
//require ROOT_PATH . 'src/SlimStarter/Bootstrap.php';   #autoload doesnt seem to be working on the home mac, but it works fine on the bakellab vm
//require VENDOR_PATH . 'slim/views/Slim/Views/Twig.php';

require_once VENDOR_PATH.'autoload.php';
\Slim\Slim::registerAutoloader();

$config = array(
    'path.root'     => ROOT_PATH,
    'path.public'   => PUBLIC_PATH,
    'path.app'      => APP_PATH,
    'path.module'   => MODULE_PATH
);

#load database and slim configuration files
foreach (glob(APP_PATH.'config/*.php') as $configFile) {

    require $configFile;
}

/** Merge cookies config to slim config */
if(isset($config['cookies'])){
    foreach($config['cookies'] as $configKey => $configVal){
        $config['slim']['cookies.'.$configKey] = $configVal;
    }
}


$app = new \Slim\Slim($config['slim']);   #set template directory and twig

//$response = $app->response();
//$response->header('Access-Control-Allow-Origin', '*');
$app->response->header('Expires: Thu, 15 Apr 2016 20:00:00 GMT');
//$response->write(json_encode($data));
$app->response->headers->set('Access-Control-Allow-Origin', '*');
$app->add(new \Slim\Middleware\SessionCookie($config['cookies']));
$app->config('debug', true);    //change to false in production

$app->notFound(function () use ($app) {
    $app->render('notFound.twig');
});


$env = $app->environment();
$env['salt'] = $config['database']['connections']['salt']['salt'];
$env["dbtables"]=$config["dbtables"];
$env["rules"]=$config["rules"];

//set twig or mustache: https://github.com/codeguy/Slim-Extras


#$app->model = new sampleModel("a");
#$app->model->sayHello();



$starter  = new \SlimStarter\Bootstrap($app);


$starter->setConfig($config);  #contains database connection info

$starter->boot();

require ROOT_PATH .'./app/routes.php';



$app->get('/test', function () {
    echo "Hello";
});

$app->get('/hello/:name', function ($name) {
    echo "Hello, $name";
});



return $starter;
