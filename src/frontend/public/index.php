<?php

$_SERVER["REQUEST_URI"] = str_replace("/frontend/","/",$_SERVER["REQUEST_URI"]);

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Events\Event;
use Phalcon\Logger;
use Phalcon\Events\Manager as EventsManager;

$config = new Config([]);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('BASE_URL', 'http://192.168.2.42:8080/api');
define('BASE_URI','http://localhost:8080/frontend');

require_once '../app/vendor/autoload.php';

$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);

$loader->registerNamespaces(
    [
        'App\Filter' => APP_PATH.'/components',
    ]
);


$loader->register();


$container = new FactoryDefault();

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

$container->set(
    'mongo',
    function () {
        $mongo =  new \MongoDB\Client('mongodb://mongo', array('username'=>'root',"password"=>'password123'));

        return $mongo->MyApp;
    },
    true
);

//Setting the di container for thee escaper
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
        return 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2NTA5NTcyNDYsImV4cCI6MTY1MDk2MDI0Niwicm9sZSI6ImFudWdyYWhAY2VkY29zcy5jb20ifQ.Kj-UU5Kn22Rd4XzlLtboyvUQOYlyQ5XaYI-e1P4lHBQ';
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

