<?php

use Phalcon\Mvc\Controller;

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
        if(empty($loginDetail))
        {
            $this->view->errormsg="Invalid Credentials";
        } else {
            $this->response->redirect(BASE_URI.'/users/dashboard');
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
            $db=$this->mongo;
            $login = new Login();
            $login->saveUserData($data, $db);
           $this->response->redirect(BASE_URI.'/login/index');
        }
    }

    /**
     * function to generate the token
     *
     * @param [type] $data
     * @return void
     */
    private function generateToken($data)
    {
        $client = new Client();
        $url=BASE_URL.'/user/getToken/'.$data['email'];
        return $client->request('GET',$url)->getBody()->getContents();
    }
}