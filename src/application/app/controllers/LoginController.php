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
            $this->response->redirect('/products/index');
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
            // $data['token']=$token;
            // $db=$this->mongo;
            // $login = new Login();
            // $login->saveUserData($data, $db);
            $this->view->token=$token;
        }
    }


    private function generateToken($data)
    {
        $client = new Client();
        $url=BASE_URL.'/user/getToken/'.$data['email'];
        return $client->request('GET',$url)->getBody()->getContents();
    }
}