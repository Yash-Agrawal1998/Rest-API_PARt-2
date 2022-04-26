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
            'user_fullname' => $data['fullname'],
            'user_email' => $data['email'],
            'user_password' => $data['password'],
        );
        $response=$db->users->insertOne($userDetail);
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