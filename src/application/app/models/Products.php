<?php


use  Phalcon\Mvc\Model;

class Products extends Model
{
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
            'categoryName' => $data['categoryName'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'labelValue' => null
        );
        $res=$db->products->insertOne($productDetail);
        return json_encode($res->getInsertedId());
    }

    /**
     * function to find all the products
     *
     * @param [object] $db
     * @return object
     */
    public function findProducts($db)
    {
        return $db->products->find();
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
        $productid = [
            "_id" => new \MongoDB\BSON\ObjectId($productid)
        ];
        $db->products->deleteOne($productid);
    }

    /**
     * function to find the product by id
     *
     * @param [string] $productid
     * @param [object] $db
     * @return void
     */
    public function findProductById($productid, $db)
    {
        $productid = [
            "_id" => new \MongoDB\BSON\ObjectId($productid)
        ];
        return $db->products->findOne($productid);
    }

    /**
     * function to update the product information
     *
     * @param [object] $db
     * @param [array] $data
     * @param [string] $productid
     * @return void
     */
    public function updateProduct($db, $data, $productid)
    {
        $id=new \MongoDB\BSON\ObjectId($productid);
        $productDetail=array(
            'name' => $data['name'],
            'categoryName' => $data['categoryName'],
            'price' => $data['price'],
            'stock' => $data['stock'],
        );
        $db->products->updateOne(["_id"=>$id], ['$set'=>$productDetail]);
    }
}