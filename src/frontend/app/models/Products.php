<?php

use Phalcon\Mvc\Model;
use GuzzleHttp\Client;

class Products  extends Model
{
    /**
     * function to get the products information from database
     *
     * @param [object] $db
     * @param [string] $token
     * @return array
     */
    public function getProducts($db, $token)
    {
        echo 'Save Product';
        $data= $db->products->find()->toArray();
        if(empty($data)) {
            $data=$this->fetchDataFromAPI($token);
            foreach($data as $value)
            {
                print_r($value);
                $tdata=[
                    'api_id' => $value['_id']['$oid'],
                    'name' => $value['name'],
                    'categoryName' => $value['categoryName'],
                    'price' => $value['price'],
                    'stock' => $value['stock'],
                    'labelValue' => $value['labelValue'],   
                ];
                $db->products->insertOne($tdata);
            }
            $data= $db->products->find()->toArray();
            return $data;
        } else {
            return $data;
        }
    }

    /**
     * function to fetc the products information from API
     *
     * @param [string] $token
     * @return JSON
     */
    private function fetchDataFromAPI($token)
    {
        $client= new Client();
        $url=BASE_URL.'/products/getAll/bearer='.$token;
        $data=json_decode($client->request('GET',$url)->getBody()->getContents(), true);
        return $data;
    }

    /**
     * function to find the product by id of product
     *
     * @param [type] $id
     * @param [object] $db
     * @return array
     */
    public function findProductbyId($id, $db)
    {
        return $db->products->find(["api_id"=> $id])->toArray();
    }


    /**
     * function to update the stock on the place of order
     *
     * @param [string] $product_id
     * @param [object] $db
     * @param [string] $quantity
     * @param [object] $productData
     * @return void
     */
    public function updateStock($product_id, $db, $quantity, $productData)
    {
        $stock= $productData[0]->stock - $quantity;
        $stockUpdate=[
            'stock' => $stock
        ];
        $res=$db->products->updateOne(["api_id"=> $product_id], ['$set'=>$stockUpdate]);
    }

     /**
     * function to save the products detail
     *
     * @param [array] $data
     * @param [object] $db
     * @return void
     */
    public function saves($data, $db)
    {   
        $productDetail=array(
            'name' => $data['name'],
            'api_id' => $data['api_id'],
            'categoryName' => $data['categoryName'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'labelValue' => null
        );
        $res=$db->products->insertOne($productDetail);
    }

        /**
     * function to delete the product
     *
     * @param [string] $productid
     * @param [object] $db
     * @return void
     */
    public function deleteProduct($productid, $db)
    {
        $db->products->deleteOne(['api_id' =>$productid]);
    }

}

