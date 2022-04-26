<?php


use Phalcon\Mvc\Controller;
use Multiple\Admin\Models\Products;
use GuzzleHttp\Client;
//use Multiple\Admin\Models\Products;

class ProductsController extends Controller
{
    public function indexAction()
    {
        echo "Products";
        if($this->request->isPost() === true)
        {
            //print_r($this->session->get('userdetail'));
            $data=$this->request->get();
            print_r($data);
            switch($data['operation'])
            {
                case 'addProduct':
                    $this->mongo->addProductsWebHooks->insertOne(['url' =>$data['product_url']]);
                    break;
                
                case 'updateProduct':
                    $this->mongo->updateProductsWebHooks->insertOne(['url' =>$data['product_url']]);
                    break;

                case 'deleteProduct':
                    $this->mongo->deleteProductsWebHooks->insertOne(['url' =>$data['product_url']]);
                    break;
            }
        }
    }


    /**
     * function to add users
     *
     * @param [type] $userData
     * @return void
     */
    public function addProductsAction()
    {   
        if($this->request->isPost() === true)
        {
            $data=$this->request->getPost();
            $data=$this->escape->sanitizeData($data);
            $event=$this->events;
            $db=$this->mongo;
            $product = new \Products();
            $id=json_decode($product->saves($data, $db), true);
            $data['labelValue']=null;
            $data['api_id']=$id['$oid'];
            $data=$event->fire('notifications:addProductNotification', $this, $data); 
            $this->response->redirect(BASE_URI."/products/showProducts");
        }
    }

 
    /**
     * functon to display users detail
     *
     * @return void
     */
    public function showProductsAction()
    {
        $this->view->productData=$this->mongo->products->find()->toArray();
    }

     /**
     * function to delete the product
     *
     * @return void
     */
    public function deleteProductAction()
    {
        $db=$this->mongo;
        $id=$this->request->getQuery('id');
        echo $id;
        $data=[
            'product_id' => $id
        ];
        $event=$this->events;
        $data=$event->fire('notifications:deleteProductNotification', $this, $data);
        $product = new \Products();
        $product->deleteProduct($id, $db);
        $this->response->redirect(BASE_URI.'/products/showProducts');
    }

     /**
     * function to edit the product
     *
     * @return void
     */
    public function editProductAction()
    {
        $db=$this->mongo;
        $id=$this->request->getQuery('id');
        $product = new Products();
        $this->view->data=$product->findProductById($id, $db);;
        if($this->request->isPost() === true)
        {
            $data=$this->request->get();
            $product->updateProduct($db,$data,$id);
            $this->response->redirect("/admin/products/showProducts");
        }
    }

    /**
     * function to generate the token
     *
     * @return void
     */
    public function generateTokenAction()
    {
        $client = new Client();
        $url=BASE_URL.'/user/getToken/admin';
        $this->session->set('token',$client->request('GET',$url)->getBody()->getContents());
        $this->response->redirect('/products/index');
    }
}