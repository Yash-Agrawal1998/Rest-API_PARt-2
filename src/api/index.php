<?php

namespace Multiple\Api;

use Phalcon\Mvc\Micro;
use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;

require_once('./vendor/autoload.php');

$loader =new Loader();

/*******************************************Registerin the Namespaces************************/
$loader->registerNamespaces(
    [
        'Api\Handlers' => './handler',
        'App\Filter' => './components',
    ]
);

$loader->register();

$container = new FactoryDefault();

$event =new EventsManager();
$event->attach(
    'notifications',
    new \App\Filter\NotificationListeners()
);

$container->set(
    'event',
    $event
);

/*******************************************Registering the database in our container*********/
$container->set(
    'mongo',
    function () 
    {
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

$prod = new \Api\Handlers\Products(); 

$app = new Micro($container);

//public function before(callable $handler):Micro{}
$app->get(
    '/invoices/view/{id}/{where}/{limit}/{page}',
    [
        $prod,
        'get'
    ]
);

//Setting end point for placing the order
$app->post(
    '/api/order/place/bearer={bearer}',
    function($bearer)
    {
        $prod = new \Api\Handlers\Products(); 
        echo $prod->addOrder($this->mongo, $bearer);
    } 
);

$app->before(
    function () use ($app) {
        //echo "hello";
        // $request = new Request();
        // $tokenByUser= $request->get('token');
        // if (is_null($tokenByUser)) {
        //     echo json_encode(['msg' => "Please Provide Token"]);
        //     die;
        // } else {
        //     // Validate Token time Expiry
        // }
    }
);


//Setting end point to list all order
$app->get(
    '/api/order/list/id={email}/bearer={bearer}',
    function($email, $bearer)
    {
        $prod = new \Api\Handlers\Products(); 
        echo $prod->listOrder($this->mongo, $email, $bearer);
    } 
);

//Url for find for all products on limit  
$app->get(
    '/api/products/getAll/bearer={bearer}',
    function($bearer)
    {
        $prod = new \Api\Handlers\Products(); 
        $data= $prod->displayAllData($this->mongo, $bearer);
        echo $data;
    }
);

//Url for find for all products on limit  
$app->get(
    '/api/products/get/{limit}/{page}/bearer={bearer}',
    function($limit, $page, $bearer)
    {
        $prod = new Api\Handlers\Products(); 
        $data= $prod->displayData($this->mongo, $bearer, $limit, $page);
        echo $data;
    }
);

//Setting the end points to list the product
$app->get(
    '/api/products/get/bearer={bearer}',
    function($bearer)
    {
        $prod = new \Api\Handlers\Products(); 
        $data= $prod->displayData($this->mongo,$bearer);
        echo $data;
    }
);


//Setting end point for searching product
$app->get(
    '/api/products/search/{keyword}/bearer={bearer}',
    function($keyword, $bearer)
    {   
        $prod = new Api\Handlers\Products(); 
        $data= $prod->searcData($this->mongo, $keyword, $bearer);
        echo $data;
    }
);

//setting end points for generationg the token
$app->get(
    '/api/user/getToken/{role}',
    function($role)
    {   
        $prod = new \Api\Handlers\Products(); 
        echo $prod->generateToken($role);
    }
);

//setting end points for updating the order
$app->put(
    '/api/order/update/bearer={bearer}',
    function($bearer)
    {
        $prod = new \Api\Handlers\Products(); 
        echo $prod->updateOrder($this->mongo,$bearer);
    }
);


// Setting the error message for nvalid controller
$app->notFound(
    function () use($app){
        $message = 'Nothing to see here. Move along....';
        $app->response
            ->setStatusCode(404, 'Not Found')
            ->sendHeaders()
            ->setContent($message)
            ->send()
        ;
    }
);


$app->handle(
    $_SERVER["REQUEST_URI"]
);