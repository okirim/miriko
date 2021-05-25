## how to build PHP framework

#### routes
`Router::httpMethod(url,[MyController,'method']);`
example 1:
`Router::post('/login', [AuthController::class, 'login']);`
example 2:
`Router::patch('/posts/{id}',[PostController::class,update])`
#####request params:
example 1:
`Request::getParams('id')`
#####request body
`Request::Body('username')`
#### validation
```
<?php

namespace App\rules;

use App\models\User;

class UserRules
{
    public static function login(array $fields)
    {
        $email = $fields['email'];
        $password = $fields['password'];
        $rules = [
            [$email, 'required email', 'invalid email'],
            [$password, 'required string', 'invalid password '],
        ];

        if (!User::isValid($rules)) {
            return User::validation_error_response();
        }
        return true;
    }

    public static function register(array $fields)
    {
        $email = $fields['email'];
        $password = $fields['password'];
        $username = $fields['username'];
        $password_confirm = $fields['password_confirm'];
        $rules = [
            [$username, 'required', 'invalid username '],
            [$email, 'required email unique:email|users', 'invalid email'],
            [$password, 'required string', 'invalid password '],
            [$password_confirm, 'required string match', 'password confirm is invalid', $password]
        ];

        if (!User::isValid($rules)) {
            return User::validation_error_response();
        }
        return true;
    }
}
```
`how to use it in controller:`
```
   /**
     * @param $email
     * @param $password
     * @return bool|false|string
     */
    protected static function validatePasswordAndEmail($email, $password)
    {
        return UserRules::login(['email' => $email, 'password' => $password]);
    }
```
####Events
`register event`
```
namespace App\listeners;


use App\core\Event;


abstract class AddEventListener
{
    protected static array $eventListener = [
        'event_name' => [MyEventClass, 'method']
    ];

}
```
`example: `
```
namespace App\listeners;


use App\core\Event;


abstract class AddEventListener
{
    protected static array $eventListener = [
        'email-verify' => [EmailVerificationListener::class, 'handle']
    ];

}
```
`create event example:`
```
<?php

namespace App\listeners;

use App\core\mails\Mail;


class EmailVerificationListener
{

    public static function  handle($user,$token)
    {
        $email = Mail::make();
        $email->from('okirimkadiro@gmail.com')
            ->to($user->email)
            ->subject('verify-email')
            ->view('confirmation_mail.php', ['token' => $token])
            ->send();
    }

}
```
`dispatch event:`
```
 Event::Dispatch('email-verify', [$param1, $param2]);
```
`example: `
```
 Event::Dispatch('email-verify', [$user, $token]);
```
####middleware
`example: create auth middleware `
```
<?php


namespace App\core\middlewares;

use App\core\exceptions\Exception;
use App\core\Request;
use App\core\Router;


class AuthMiddleware extends BaseMiddleware
{
    public array $actions;
    /**
     * AuthMiddleware constructor.
     * @param array $actions
     */
    public function __construct(array $actions=[])
    {
        $this->actions = $actions;
    }

    public function handle(){

        if(Request::guest()){
            if (empty($actions) && in_array(Router::getAction(),$this->actions)){
               Exception::make('forbidden route',401);
            }
        }
       return true;
    }
}

```
`example:apply middleware in my controller`
```

    public static function middleware()
    {
        static::registerMiddleware(new AuthMiddleware(['createPost','deletePost']));
        static::registerMiddleware(new AdminMiddleware(['deletePost']));
        return static::applyMiddleware();
    }

    public static function createPost(){}
    public static function deletePost(){}
```
#### Response
```
Response::json_response($ata,$status,$statusCode);
Response::json_response_error($error,$status,$statusCode);
```
#### Exceptions
```
Exception::make($message,$error_code);
NotFoundException::make();
UnAuthorizedException::make();
InternalServerErrorException::make();
```


