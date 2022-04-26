<?php

namespace App\Filter;
use GuzzleHttp\Client;

use Phalcon\Events\Event;

class NotificationListeners
{
    public function provideUpdateOfOrder($event, $component, $product)
    {
        //echo $productId;
        $client = new Client();
        $url='http://192.168.2.42:8080/frontend/products/getData';
        $res=$client->request('POST',$url,['form_params'=> $product])->getBody()->getContents();
        print_r($res);
    }
}