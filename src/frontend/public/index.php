<?php

$_SERVER["REQUEST_URI"] = str_replace("/frontend/","/",$_SERVER["REQUEST_URI"]);

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Config\ConfigFactory;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Events\Event;
use Phalcon\Logger;
use Phalcon\Events\Manager as EventsManager;

$config = new Config([]);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('BASE_URL', 'http://192.168.2.42:8080/api');
define('BASE_URI','http://192.168.2.42:8080/frontend');

$debug = new \Phalcon\Debug();
$debug->listen();
    

require_once '../app/vendor/autoload.php';

$profiler = new \Fabfuel\Prophiler\Profiler();

$toolbar = new \Fabfuel\Prophiler\Toolbar($profiler);
$toolbar->addDataCollector(new \Fabfuel\Prophiler\DataCollector\Request());
echo $toolbar->render();

$loader = new Loader();

//Registering the directories
$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);


//Registering the namespaces
$loader->registerNamespaces(
    [
        'App\Filter' => APP_PATH.'/components',
    ]
);


$loader->register();


$container = new FactoryDefault();

//setting the container for the view
$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/frontend');
        return $url;
    }
);
$application = new Application($container);

//Setting the di container for the config 
$container->set(
    'config',
    function () {
        $filename=APP_PATH.'/storage/constant.php';
        $factory= new ConfigFactory();
        return $factory->newInstance('php', $filename);
    }
);

//Setting the Di container for the mongo
$container->set(
    'mongo',
    function () {
        $mongo =  new \MongoDB\Client('mongodb://mongo', array('username'=>'root',"password"=>'password123'));

        return $mongo->MyApp;
    },
    true
);

//Setting the di container for the escaper
$container->set(
    'escape',
    function()
    {
        $escape =new \App\Filter\FilterData();
        return $escape;
    }
);

//Setting the token value in the di container
$container->set(
    'token',
    function()
    {
        return 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJVdGthcnNoIiwiYXVkIjoiaHR0cDovL2NlZGNvc3MuY29tIiwiaWF0IjoxNjUwOTcxMzUwLCJuYmYiOjE2NTA5NzEyOTAsImV4cCI6MTY1MTA1Nzc1MCwiZW1haWwiOiJBcnJheSJ9.ZbKAsTKCPPLCc48rXgETyNiCsk3Yl0N1axeLHzKaido';
    }
);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo"Error";
    echo 'Exception: ', $e->getMessage();
}

