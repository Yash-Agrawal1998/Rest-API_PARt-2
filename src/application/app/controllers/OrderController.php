<?php

use Phalcon\Mvc\Controller;
use GuzzleHttp\Client;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class OrderController extends Controller 
{

    /**
     * function to list all the Orders
     *
     * @return void
     */
    public function listOrderAction()
    {
        $bearer=$this->token;
        $decoded = $this->decodeToken($bearer);
        $client = new Client();
        $url=BASE_URL.'/order/list/id='.$decoded->role.'/bearer='.$bearer;
        //$myorder=json_decode($client->request('GET', $url)->getBody()->getContents());
        $this->view->orderDetail=json_decode($client->request('GET', $url)->getBody()->getContents(),true);
    }


    /**
     * function to place the order
     *
     * @return void
     */
    public function placeOrderAction()
    {
        $client = new Client();
        $url=BASE_URL.'/products/get/bearer='.$this->token;
        echo 'order37';
        $productDetail=$client->request('GET',$url)->getBody()->getContents();
        $this->view->productDetail=json_decode($client->request('GET',$url)->getBody()->getContents(), true);

        //$this->view->productDetail=$this->mongo->products->find();
        if($this->request->isPost() === true)
        {
            $bearer=$this->token;
            $data=$this->request->getPost();
            $decoded = $this->decodeToken($bearer);
            $formdata=[
                'form_params'=>[
                    'c_email' =>$decoded->role,
                    'name' => $data['c_name'],
                    'pname' => $data['product_name'],
                    'quantity' => $data['p_quantity'],
                    'status' => 'paid'
                ]
            ];
            $url=BASE_URL.'/order/place/bearer='.$bearer;
            $orderId=json_decode($client->request('POST',$url,$formdata)->getBody()->getContents(),true);
            $order=array(
                'orderId' =>$orderId['$oid']
            );
            $this->mongo->orderId->insertOne($order);
            $this->response->redirect(BASE_URI.'/Order/listOrder');
        }
    }

    /**
     * function to decode the token
     *
     * @param [string] $bearer
     * @return object
     */
    private function decodeToken($bearer)
    {
        $key = "example_key";
        $decode=JWT::decode($bearer, new Key($key, 'HS256'));
        return $decode;
    }

}