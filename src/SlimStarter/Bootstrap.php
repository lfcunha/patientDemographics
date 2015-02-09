<?php
/**
 * Created by PhpStorm.
 * User: luiscunha
 * Date: 7/2/14
 * Time: 12:33 AM
 */

namespace SlimStarter;
#require_once __DIR__."/../../app/models/auxiliaryTablesModel.php";

use PDO;
use auxiliary;

class Bootstrap
{
    protected $app;
    protected $config;
    protected $dsn;

    public function __construct(\Slim\Slim $app = null){
        $this->app = $app;
    }

    public function setConfig($config){
        $this->config = $config;
        foreach ($config as $key => $value) {
            $this->app->config($key, $value);
        }
        #var_dump($config['database']['connections']['mysql']);

    }



    public function setApp(\Slim\Slim $app){
        $this->app = $app;
    }


    public function bootDB($config){


        $app = $this->app;

	//echo $this->dsn;

       // Set singleton value
        $this->app->container->singleton('db', function () use ($app, $config) {
            $hostname=$config['connections']['mysql']['host'];
            #$database=$config['connections']['mysql']['database'];
            $database=$password=$config['connections']['mysql']['database'];;

            $dsn = 'mysql:host=' . $hostname . ';dbname=' . $database;
                    $username=$config['connections']['mysql']['username'];
                    $password=$config['connections']['mysql']['password'];
            try
            {
                $options = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    PDO::FETCH_ASSOC
                );
                //$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . $database;
                $DBH= new \PDO($dsn, $username, $password, $options);  //change this to be properties in config
            }
            catch (PDOException $e)
            {
                echo $e->getMessage();
            }
            return $DBH;


        });

        // Get singleton value
        #$pdo = $this->app->db;

   }

/*
    public function bootAuxiliaryTables(){
        $atm=new auxiliaryTablesModel();
        $this->app->container->singleton('auxiliaryTables', function () use ($app) {
            return $atm;});
    }
*/

    //set up twig

    public function boot(){
        $this->bootDB($this->config['database']);
        #$this->bootAuxiliaryTables();
    }



    public function run(){
        $this->app->run();

    }


    }








