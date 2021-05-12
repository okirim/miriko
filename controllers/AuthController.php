<?php


namespace App\controllers;


use App\core\JWT;
use App\core\Mail;
use App\core\Query;
use App\core\Request;
use App\core\Response;
use App\core\Rules;
use App\core\View;
use App\models\User;
use App\rules\UserRules;
use Carbon\Carbon;

class AuthController
{

    public function loginPage()
    {

        View::setLayout('auth');
        return View::render('login');
    }

    public function login()
    {
//        $exp=Carbon::now()->addHours(6)->getTimestamp();
//        $payload=[
//            'user_id' => 1,
//        ];
//        $token = JWT::create($payload);
        $jwt=Request::getToken();
        $token= JWT::validate($jwt);
        echo '<pre>';
        var_dump($token);
        echo '</pre>';
        exit();
        $email = Request::Body('email');
        $password = Request::Body('password');
        $isValid = UserRules::login(['email' => $email, 'password' => $password]);

        if ($isValid !== true) {
            return UserRules::login(['email' => $email, 'password' => $password]);
        }
        $user = User::Olivine()::findOne(['email' => $email]);
        if (!$user) {
            return Response::json_response_error('user not found');
        }
        $checkPassword = password_verify($password, $user->password);
        if (!$checkPassword) {
            return Response::json_response_error('invalid email or password');
        }
        return Response::json_response($user);
    }

    public function register()
    {

        $username = Request::Body('username');
        $email = Request::Body('email');
        $password = Request::Body('password');
        $password_confirm = Request::Body('confirm_password');

        $fields = ['email' => $email,
            'password' => $password,
            'username' => $username,
            'password_confirm' => $password_confirm
        ];
        $isValid = UserRules::register($fields);
        if ($isValid !== true) {
            return UserRules::register($fields);
        }
        $data = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_ARGON2I),
        ];
//      $user = Query::create('users', $data);
        $user = User::Olivine()::create($data);
//        $users=User::leftJoin(['comments','posts']);
        //User::hasMany('comments');
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