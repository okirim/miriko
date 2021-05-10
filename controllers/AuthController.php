<?php


namespace App\controllers;


use App\core\Mail;
use App\core\Query;
use App\core\Request;
use App\core\Response;
use App\core\Rules;
use App\core\View;

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

        $username = Request::Body('username');
        $email = Request::Body('email');
        $password = Request::Body('password');
        $password_confirm = Request::Body('confirm_password');
        $validate = [
            [$username, 'required', 'invalid username '],
            [$email, 'required email unique:email|users', 'invalid email'],
            [$password, 'required string', 'invalid password '],
            [$password_confirm, 'required string match', 'password confirm is invalid', $password]
        ];
        if (Rules::Validator($validate)) {
            return Rules::Validator($validate);
        }
        $data = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_ARGON2I),
        ];
        $user = Query::create('users', $data);
        return Response::json_response($user);

    }

    public function registerPage()

    {
        $mail = Mail::make();
        $mail->from('okirimkadiro@gmail.com')
            ->to('okirim.abdelkader.dev@gmail.com')
            ->subject('test')
            ->view('message.php',['name'=>'kadiro'])
            ->send();
       return View::render('register');

    }
}