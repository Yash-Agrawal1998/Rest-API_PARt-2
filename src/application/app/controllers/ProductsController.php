<?php


use Phalcon\Mvc\Controller;
use Multiple\Admin\Models\Products;
use GuzzleHttp\Client;

class ProductsController extends Controller
{
    public function indexAction()
    {
        echo "Products";
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
            $additionalField=$this->selectAdditionalField($data);
            if (empty($additionalField)) {
                $additionalField=null;
            }
            $data['additionalFields']=$additionalField;
            $db=$this->mongo;
            $product = new Products();
            $product->saves($data, $db);
            $this->response->redirect("/admin/products/showProducts");
        }
    }

    /**
     * function to create the array for additional field
     *
     * @param [type] $data
     * @param integer $checkPoint
     * @return void
     */
    private function selectAdditionalField($data,$checkPoint=4)
    {
        $key;
        $count=0;
        $count2=1;
        $addField=array();
        foreach($data as $keyData=>$value)
        {
            if ($count<$checkPoint) {
                $count ++;
                continue;
            }
            if($keyData === "price1" or $keyData === 'labelName1')
            {
                return $addField; 
            } else {
                if ($count2==1) {
                    $key=$value;
                    $count2++;
                } else {
                    $addField[$key]=$value;
                    $count2=1;
                }
            }
        }
        return $addField; 
    }


    /**
     * functon to display users detail
     *
     * @return void
     */
    public function showProductsAction()
    {
        $client = new Client();
        $url=BASE_URL.'/products/get/bearer='.$this->session->token;
        $productDetail=$client->request('GET',$url);
        $this->view->productDetail=json_decode($client->request('GET',$url)->getBody()->getContents());
        // try{
        //     $decoded = JWT::decode($bearer, new Key($key, 'HS256'));
        //     $data=$db->products->find(
        //         [],
        //         [
        //             'limit' => intval($limit),
        //             'skip'  => intval($limit*($page-1))
        //         ]
        //     )->toArray();
        //     return json_encode($data);
        // } catch(\Exception $e)
        // {
        //     echo "Token has expired";
        // }
        // $product = new Products();
        // $db=$this->mongo;
        // $productDetail= $product->findProducts($db);
        // $this->view->productDetail= $product->findProducts($db);
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
        $product = new Products();
        $product->deleteProduct($id, $db);
        $this->response->redirect('/admin/products/showProducts');
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
     * function to find the additional information
     *
     * @return void
     */
    public function findInfromationAction()
    {
        $data=$this->request->getPost();
        $db=$this->mongo;
        $product = new Products();
        $searchResponse=$product->findProductById($data['id'], $db);
        return json_encode(array('data'=>$searchResponse));
    }
   

    public function generateTokenAction()
    {
        $client = new Client();
        $url=BASE_URL.'/user/getToken/admin';
        $this->session->set('token',$client->request('GET',$url)->getBody()->getContents());
        $this->response->redirect('/products/index');
    }
}