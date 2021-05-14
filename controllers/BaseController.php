<?php

namespace App\controllers;

use App\core\Application;
use App\core\middlewares\BaseMiddleware;
use App\core\Request;

class BaseController
{
    public static array $middlewares;

    public static function registerMiddleware(BaseMiddleware $middleware)
    {
        self::$middlewares[] = $middleware;

    }

    public static function applyMiddleware()
    {
        foreach (self::$middlewares as $middleware) {
            if($middleware->execute()!==true){
              return  $middleware->execute();
            }
        }
    }

}