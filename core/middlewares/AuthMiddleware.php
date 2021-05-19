<?php


namespace App\core\middlewares;

use App\core\exceptions\Exception;
use App\core\Request;
use App\core\Response;
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