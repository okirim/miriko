<?php


namespace App\core\middlewares;


use App\controllers\BaseController;
use App\core\Exception;
use App\core\Request;
use App\core\Response;
use App\core\Router;
use App\models\User;

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
            if (empty($actions) && in_array(Router::$action,$this->actions)){
                return Response::json_response_error('forbidden route',401);
            }
        }
       return true;
    }
}