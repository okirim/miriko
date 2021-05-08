<?php


namespace App\controllers;


use App\core\Query;
use App\core\Request;
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
            [$username, 'required ', 'invalid username '],
            [$email, 'required email', 'invalid email'],
            [$password, 'required string', 'invalid password '],
            [$password_confirm, 'required string match', 'password confirm is invalid', $password]
        ];
        if (Rules::Validator($validate)) {
            return Rules::Validator($validate);
        }
        $data=[
            'username'=>Request::Body('username'),
            'email'=>Request::Body('email'),
            'password'=>Request::Body('password'),
            ];
        $user=Query::create('users',$data);
        echo '<pre>';
        var_dump($user);
        echo '</pre>';

    }

    public function registerPage()

    {
$users=Query::find('users');
echo '<pre>';
var_dump($users);
echo '</pre>';
//        View::setLayout('auth');
//        return View::render('register');
    }
}