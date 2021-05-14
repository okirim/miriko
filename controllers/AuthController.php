<?php


namespace App\controllers;


use App\core\authentification\JWT;
use App\core\mails\Mail;
use App\core\middlewares\AuthMiddleware;
use App\core\Request;
use App\core\Response;
use App\core\View;
use App\models\User;
use App\rules\UserRules;


class AuthController extends BaseController
{

    public static function middleware()
    {
       static::registerMiddleware(new AuthMiddleware(['test']));
      return static::applyMiddleware();
    }

    public function loginPage()
    {

        View::setLayout('auth');
        return View::render('login');
    }

    public function login()
    {
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
        $jwt = JWT::create(['user_id' => $user->id]);
        return Response::json_response(['user' => $user, 'token' => $jwt]);
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

        $user = User::Olivine()::create($data);
        $token = JWT::create(['user_id' => $user->id]);
        $mail = Mail::make();
        $mail->from('okirimkadiro@gmail.com')
            ->to('okirim.abdelkader.dev@gmail.com')
            ->subject('test')
            ->view('confirmation_mail.php', ['token' => $token])
            ->send();

        return Response::json_response($user);

    }

    public function validateEmail()
    {
        try {
            $token = Request::Body('token');
            $payload = JWT::validateOrFail($token);
            if (empty($payload) || empty($payload->user_id)) {
                return Response::json_response_error($payload);
            }

            $response = User::Olivine()::findByIdAndUpdate($payload->user_id, ['email_verified' => true]);

            if ($response) {
                return Response::json_response('validation success');
            }
        } catch (\Exception $err) {
            return Response::json_response_error($err->getMessage());
        }

    }

    public function resetPassword()
    {

    }

    public function test()
    {

        $user = Request::me();
        if (isset($user->id)) {
            return Response::json_response('hello');
        }
        return Response::json_response_error('error req');

    }

    public function registerPage()

    {
        //        $users=User::leftJoin(['comments','posts']);
        //User::hasMany('comments');

        ////////////////////////
//        $user = Query::create('users', $data);
//        $mail = Mail::make();
//        $mail->from('okirimkadiro@gmail.com')
//            ->to('okirim.abdelkader.dev@gmail.com')
//            ->subject('test')
//            ->view('confirmation_mail.php',['name'=>'zino'])
//            ->send();

        return View::render('register');

    }
}