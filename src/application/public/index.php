<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;

$_SERVER["REQUEST_URI"] = str_replace("/application/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
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

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('BASE_URL', 'http://192.168.2.42:8080/api');
define('BASE_URI', 'http://localhost:8080/application');

require_once BASE_PATH.'/app/vendor/autoload.php';


// Register an autoloader
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
        'App\ModelsData' => APP_PATH.'/models'
    ]
);



$loader->register();

$container = new FactoryDefault();


// $event =new EventsManager();
// $event->attach(
//     'notifications',
//     new App\Filter\NotificationListeners()
// );

// $event->attach(
//     'application:beforeHandleRequest',
//     new App\Filter\NotificationListeners()
// );

// $container->set(
//     'EventManager',
//     $event
// );


$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

//creating the object of the session class
$container->set(
    'session',
    function() {
        $session= new Manager();
        $files= new Stream(
            [
                'savePath' => '/tmp' 
            ]
        );
        $session->setAdapter($files);

        $session->start();
        return $session;
    }
);

/**********************************Storing the token**************/
$container->set(
    'token',
    function ()
    {
        $token='eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2NTA2MjI3MTUsImV4cCI6MTY1MDYyNTcxNSwicm9sZSI6InRhbnZlZXJAZ21uYWlsLmNvbSJ9.FM-ksj5-f66bljZRJxLuYdrcRcWlss5LXG2k1PAj03c';
        return $token;
   }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);
$application = new Application($container);

//Setting the mongo container for store
$container->set(
    'mongo',
    function () {
        $mongo =  new \MongoDB\Client('mongodb://mongo', array('username'=>'root',"password"=>'password123'));

        return $mongo->AppStore;
    },
    true
);

  //Registering the escaper class
  $container->set(
    'escape',
    function() {
        $escape =new \App\Filter\FilterData();
        return $escape;
    }
);

  //Creating the object of the logger class
  $container->set(
    'logger',
    function(){
        $adapter = new  \Phalcon\Logger\Adapter\Stream(APP_PATH.'/logs/login.log');
        $logger  = new Logger(
            'messages',
            [
                'main' => $adapter,
            ]
        );
        return $logger;
    });

//$application->setEventsManager($event);

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
