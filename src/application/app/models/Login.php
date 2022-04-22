<?php

use Phalcon\Mvc\Model;

class Login extends Model
{
    /**
     * function to save the users detail
     *
     * @param [array] $data
     * @param [object] $db
     * @return void
     */
    public function saveUserData($data, $db)
    {
        $userDetail=array(
            'user_email' => $data['email'],
            'user_role' => $data['role'],
            'user_token' => $data['token']
        );
        $response=$db->users->insertOne($userDetail);
        return json_encode($response->getInsertedId());
    }

    /**
     * find the login details 
     *
     * @param [array] $data
     * @param [object] $db
     * @return object
     */
    public function findLoginDetails($data, $db)
    {
        return $db->users->find(["user_email" => $data['email'], 'user_password' => $data['password']]);
    }
}