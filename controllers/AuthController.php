<?php


namespace App\controllers;


use App\core\Mail;
use App\core\Query;
use App\core\Request;
use App\core\Response;
use App\core\Rules;
use App\core\View;
use App\models\RegisterModel;

class AuthController
{

    public function loginPage()
    {
        View::setLayout('auth');
        return View::render('login');
    }

    public function login()
    {

    }

    public function register()
    {

//        $username = Request::Body('username') ?? '';
//        $email = Request::Body('email') ?? '';
//        $password = Request::Body('password') ?? '';
//        $password_confirm = Request::Body('confirm_password') ?? '';
        $username = '';
        $email ='';
        $password ='';
        $password_confirm = '';
         $user=new RegisterModel($username,$email,$password,$password_confirm);

//         if(!$user->isValidate()){
//             return $user->validation_error_response();
//         }
//        $data = [
//            'username' => $username,
//            'email' => $email,
//            'password' => password_hash($password, PASSWORD_ARGON2I),
//        ];
//      $user = Query::create('users', $data);
//        $user = RegisterModel::create($data);
       $user= RegisterModel::leftJoin('comments');
        return Response::json_response($user);

    }

    public function registerPage()

    {
//        $mail = Mail::make();
//        $mail->from('okirimkadiro@gmail.com')
//            ->to('okirim.abdelkader.dev@gmail.com')
//            ->subject('test')
//            ->view('message.php',['name'=>'zino'])
//            ->send();

       return View::render('register');

    }
}