<?php

use Phalcon\Mvc\Controller;
use GuzzleHttp\Client;

class ProductsController extends Controller
{
    /**
     * function to list all the products
     *
     * @return void
     */
    public function listProductsAction()
    {
        $db=$this->mongo;
        $product=new Products();
        $this->view->productinfo=$product->getProducts($db, $this->token);
    }

    /**
     * function to place order
     *
     * @return void
     */
    public function placeOrderAction()
    {
        $pId=$this->request->get('id');
        $product=new Products();
        $this->view->productdata=$product->findProductbyId($pId, $this->mongo);
        if($this->request->isPost() === true)
        {
            $client = new Client();
            $data=$this->request->getPost();
            print_r($data);
            $orderdetail=[
                'customer_email' =>$data['customer_email'],
                'customer_name' =>$data['customer_name'],
                'product_id' => $data['product_id'],
                'quantity' => $data['product_quantity'],
                'status' => 'Paid'
            ];
            $url=BASE_URL.'/order/place/bearer='.$this->token;
            $orderId=json_decode($client->request('POST',$url,['form_params' => $orderdetail])->getBody()->getContents(),true);
            echo $url;
            print_r($orderdetail);
            die;
        }

    }

    /**
     * function to handle the post request on the place of order
     *
     * @return void
     */
    public function getDataAction()
    {
        echo 'response done';
        $data=$this->request->getPost();
        $product=new Products();
        $productDetail=$product->findProductbyId($data['product_id'], $this->mongo);
        $product->updateStock($data['product_id'], $this->mongo, $data['quantity'], $productDetail);
        
    }

    /**
     * function to handle post request of API on product add
     *
     * @return void
     */
    public function addProductsAction()
    {
        echo 'product Added';
        $data=$this->request->getPost();
        $product = new \Products();
        $db=$this->mongo;
        $product->saves($data, $db);
    }

    public function updateProductsAction()
    {
        
    }

    /**
     * function to handle post request of api on delete of product
     *
     * @return void
     */
    public function deleteProductsAction()
    {
        $data=$this->request->getPost();
        $product = new \Products();
        $db=$this->mongo;
        $product->deleteProduct($data['product_id'], $db);
    }
}