<?php

namespace App\core;

class Router
{
    protected static array $routes;

    public static function get($path, $callback)
    {
        self::$routes['get'][$path] = $callback;
    }

    public static function post($path, $callback)
    {
        self::$routes['post'][$path] = $callback;
    }
    public static function patch($path, $callback)
    {
        self::$routes['patch'][$path] = $callback;
    }
    public static function put($path, $callback)
    {
        self::$routes['put'][$path] = $callback;
    }
    public static function delete($path, $callback)
    {
        self::$routes['delete'][$path] = $callback;
    }
    public static function resolve()
    {
        $path =  Request::getPath();
        $method = Request::getMethod();
        $callback = self::$routes[$method][$path] ?? false;
        if ($callback === false) {
         return  Response::json_response_error('route not found','failed',404);
//            return View::render('utils/_404');
        }
        if (is_string($callback)) {
            return View::render($callback);
        }
        return call_user_func($callback);

    }
}
