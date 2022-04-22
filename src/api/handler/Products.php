<?php

namespace Api\Handlers;

use Phalcon\Mvc\Model;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Phalcon\Di\Injectable;

class Products extends Injectable
{
    function get($select="", $where="", $limit=10, $page=1)
    {
        $products=array(
            array('select' => $select, 'where' => $where, 'limit' => $limit, "page" => $page),
            array('name' => 'Product2', 'price' => 40)
        );
        return json_encode($products);
    }

    /**
     * function to display the products list 
     *
     * @param [object] $db
     * @param integer $limit
     * @param integer $page
     * @return JSON Data
     */
    public function displayData($db, $bearer="", $limit=10, $page=1)
    {
        $key = "example_key";
        try{
            $decoded = JWT::decode($bearer, new Key($key, 'HS256'));
            $data=$db->products->find(
                [],
                [
                    'limit' => intval($limit),
                    'skip'  => intval($limit*($page-1))
                ]
            )->toArray();
            return json_encode($data);
        } catch(\Exception $e)
        {
            echo "Token has expired";
        }
    }

    /**
     * function to search the product on the basis of name
     *
     * @param [object] $db
     * @param [string] $name
     * @return string
     */
    public function searcData($db, $name, $bearer)
    {
        $key = "example_key";
        try{
            $decoded = JWT::decode($bearer, new Key($key, 'HS256'));
            $name=explode(" ", urldecode($name));
            $data=[];
            foreach($name as $key)
            {
                array_push($data, $db->products->find(['name' => ['$regex' =>$key, '$options' => '$i']])->toArray());
            }
            return json_encode($data);
        } catch(\Exception $e)
        {
            echo "Token has expired";
        }
    }

    /**
     * function to generate the token
     *
     * @param [type] $role
     * @return void
     */
    public function generateToken($role)
    {
        $key = "example_key";
        $now = new \DateTimeImmutable();
        $issued = $now->getTimestamp();
        $payload = array(
            "iat" => $issued,
            "exp" => ($issued+3000),
            'role' => $role
        );
        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }

    /**
     * function to place order
     *
     * @param [object] $db
     * @param [string] $bearer
     * @return void
     */
    public function addOrder($db, $bearer)
    {
        $key = "example_key";
        try{
            $decoded = JWT::decode($bearer, new Key($key, 'HS256'));
            $data=$this->request->getPost();
            $data=$this->escape->sanitizeData($data);
            $id = [
                "_id" => new \MongoDB\BSON\ObjectId($data['product_name'])
            ];
            $exist=$db->products->find(["_id"=>$id['_id']])->toArray();
            $result=$db->orders->insertOne($data);
            return json_encode($result->getInsertedId());
        } catch(\Exception $e)
        {
            echo $e->getMessage();
            echo "Either Token has expired or Invalid Product Id";
        }
    }

    /**
     * function to update the order status
     *
     * @param [type] $db
     * @return void
     */
    public function updateOrder($db, $bearer)
    {
        $key = "example_key";
        try{
            $decoded = JWT::decode($bearer, new Key($key, 'HS256'));
            $id = [
                "_id" => new \MongoDB\BSON\ObjectId('62615fa6cdb2bf64d80fcc13')
            ];
            $orderDetail=[
                'status' => 'dispatched',
            ];
            $res=$db->orders->updateOne(["_id"=>$id['_id']], ['$set'=>$orderDetail]);
            return 'Order updated successfully';
        } catch(\Exception $e)
        {
            echo "Token has expired";
        }
    }
    /**
     *function to list the order
     *
     * @param [object] $db
     * @param [string] $email
     * @param [string] $bearer
     * @return void
     */
    public function listOrder($db, $email, $bearer)
    {
        $key = "example_key";
        try{
            $decoded = JWT::decode($bearer, new Key($key, 'HS256'));
            $data=$db->orders->find(['c_email' => $email])->toArray();
            return json_encode($data);
        } catch(\Exception $e)
        {
            echo "Token has expired";
        }
    }
    
}