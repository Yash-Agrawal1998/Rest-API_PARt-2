<?php

use Phalcon\Mvc\Controller;
use Api\Handlers\Products;
use GuzzleHttp\Client;

class LoginController extends Controller
{   
    /**
     * function for login
     *
     * @return void
     */
    public function indexAction()
    {
       if($this->request->isPost() === true)
       {
        $data=$this->request->get();
        $data=$this->escape->sanitizeData($data);
        $db=$this->mongo;
        $login = new Login();
        $loginDetail=$login-> findLoginDetails($data, $db);
        $loginDetail=$loginDetail->toArray();
        $log=$this->logger;
        if(empty($loginDetail))
        {
            $log->error("Invalid Credentials");
        } else {
            $this->session->set('userdetail', $loginDetail);
            $log->info("Login Successful");
            $this->response->redirect(BASE_URI.'/products/index');
        }
       }
    }

    /**
     * function for signup
     *
     * @return void
     */
    public function signupAction()
    {
        if($this->request->isPost() === true)
        {
            $data=$this->request->get();
            $data=$this->escape->sanitizeData($data);
            $token=$this->generateToken($data);
            $db=$this->mongo;
            $login = new Login();
            $login->saveUserData($data, $db);
            $this->view->token=$token;
        }
    }

    /**
     * function to generate the token
     *
     * @param [array] $data
     * @return void
     */
    private function generateToken($data)
    {
        $client = new Client();
        $url=BASE_URL.'/user/getToken/'.$data['email'];
        return $client->request('GET',$url)->getBody()->getContents();
    }
}