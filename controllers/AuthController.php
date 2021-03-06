<?php


namespace App\controllers;


use App\core\authentification\JWT;
use App\core\Event;
use App\core\exceptions\Exception;
use App\core\mails\Mail;
use App\core\middlewares\AuthMiddleware;
use App\core\Request;
use App\core\Response;
use App\core\View;
use App\models\User;
use App\rules\UserRules;


/**
 * Class AuthController
 * @package App\controllers
 */
class AuthController extends BaseController
{

    /**
     * @return mixed
     */
    public static function middleware()
    {
        static::registerMiddleware(new AuthMiddleware([]));
        return static::applyMiddleware();
    }

    /**
     * @return bool|false|string
     * @throws \Exception
     */
    public static function login()
    {
        try {
            $email = Request::Body('email');
            $password = Request::Body('password');

            $isValid = self::validatePasswordAndEmail($email, $password);

            if ($isValid !== true) {
                return UserRules::login(['email' => $email, 'password' => $password]);
            }
            $user = User::Olivine()::findOne(['email' => $email]);
            if (!$user) {
                return Response::json_response_error('user not found');
            }
            if (!password_verify($password, $user->password)) {
                return Response::json_response_error('invalid email or password');
            }
            $jwt = JWT::create(['user_id' => $user->id]);
            return Response::json_response(['user' => $user, 'token' => $jwt]);
        } catch (\Exception $err) {
            throw $err;
        }

    }

    /**
     * @return bool|false|string
     * @throws \Exception
     */
    public static function register()
    {
        try {
            $username = Request::Body('username');
            $email = Request::Body('email');
            $password = Request::Body('password');
            $password_confirm = Request::Body('confirm_password');

            list($fields, $isValid) = self::validateUserInput($email, $password, $username, $password_confirm);
            if ($isValid !== true) {
                return UserRules::register($fields);
            }
            $user = self::createUser($username, $email, $password);
            $token = JWT::create(['user_id' => $user->id]);
            Event::Dispatch('email-verify', [$user, $token]);

            return Response::json_response($user);
        } catch (\Exception $err) {
            throw $err;
        }
    }


    /**
     * @param $email
     * @param $password
     * @return bool|false|string
     */
    protected static function validatePasswordAndEmail($email, $password)
    {
        return UserRules::login(['email' => $email, 'password' => $password]);
    }

    /**
     * @param $email
     * @param $password
     * @param $username
     * @param $password_confirm
     * @return array
     */
    protected static function validateUserInput($email, $password, $username, $password_confirm): array
    {
        $fields = ['email' => $email,
            'password' => $password,
            'username' => $username,
            'password_confirm' => $password_confirm
        ];
        $isValid = UserRules::register($fields);
        return array($fields, $isValid);
    }

    /**
     * @param $username
     * @param $email
     * @param $password
     * @return mixed
     */
    protected static function createUser($username, $email, $password): mixed
    {
        $data = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_ARGON2I),
        ];

        return User::Olivine()::create($data);

    }

    /**
     *
     */
    public function resetPassword()
    {

    }

    /**
     * @param $id
     * @return mixed
     */
    public static function test($id)
    {
        return $id;
    }

    /**
     * @return string|string[]
     */
    public static function registerPage()

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
    //    /**

//     * @return string|string[]
//     */
//    public static function loginPage()
//    {
//
//        View::setLayout('auth');
//        return View::render('login');
//    }
//    /**
//     * @return false|string
//     */
//    public static function validateEmail()
//    {
//        try {
//            $token = Request::Body('token');
//            $payload = JWT::validateOrFail($token);
//            if (empty($payload) || empty($payload->user_id)) {
//                return Response::json_response_error($payload);
//            }
//
//            $response = User::Olivine()::findByIdAndUpdate($payload->user_id, ['email_verified' => true]);
//
//            if ($response) {
//                return Response::json_response('validation success');
//            }
//        } catch (\Exception $err) {
//            return Response::json_response_error($err->getMessage(), 'failed', $err->getCode());
//        }
//    }
}

