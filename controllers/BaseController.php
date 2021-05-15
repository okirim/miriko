<?php

namespace App\controllers;

use App\core\Application;
use App\core\Event;
use App\core\middlewares\BaseMiddleware;
use App\core\Request;

abstract class BaseController extends Event
{
    public static array $middlewares;

    abstract public static function middleware();

    public static function registerMiddleware(BaseMiddleware $middleware)
    {
        self::$middlewares[] = $middleware;

    }

    public static function applyMiddleware()
    {
        foreach (self::$middlewares as $middleware) {
            if ($middleware->handle() !== true) {
                return $middleware->handle();
            }
        }
    }

}