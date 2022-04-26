<?php

namespace App\Filter;
use GuzzleHttp\Client;

use Phalcon\Events\Event;
/**
 * Class to handle the webhooks
 */
class NotificationListeners
{
    /**
     * function to handle the webhooks on the place of order
     *
     * @param [object] $event
     * @param [object] $component
     * @param [array] $product
     * @return void
     */
    public function provideUpdateOfOrder($event, $component, $product)
    {
        //echo $productId;
        $client = new Client();
        $url='http://192.168.2.42:8080/frontend/products/getData';
        $res=$client->request('POST',$url,['form_params'=> $product])->getBody()->getContents();
    }

    /**
     * function to handle the webhooks on the addition of product
     *
     * @param [object] $event
     * @param [object] $component
     * @param [array] $productData
     * @return void
     */
    public function addProductNotification($event, $component, $productData)
    {
        $client = new Client();
        $url=$component->mongo->addProductsWebHooks->find()->toArray();
        $client = new Client();
        foreach($url as $value)
        {
            $res=$client->request('POST',$value['url'],['form_params'=> $productData])->getBody()->getContents();
        }   
    }

    /**
     * function to handle the webhooks on the delete of products
     *
     * @param [object] $event
     * @param [object] $component
     * @param [array] $product
     * @return void
     */
    public function deleteProductNotification($event, $component, $productData)
    {
        $url=$component->mongo->deleteProductsWebHooks->find()->toArray();
        $client = new Client();
        foreach($url as $value)
        {
            $res=$client->request('POST',$value['url'],['form_params'=> $productData])->getBody()->getContents();
        }   
    }

    /**
     * function to handle the webhooks on the update of products
     *
     * @param [object] $event
     * @param [object] $component
     * @param [array] $product
     * @return void
     */
    public function updateProductNotification($event, $component, $productData)
    {
        $url=$component->mongo->updateProductsWebHooks->find()->toArray();
        $client = new Client();
        foreach($url as $value)
        {
            $res=$client->request('POST',$value['url'],['form_params'=> $productData])->getBody()->getContents();
        }   
    }
}