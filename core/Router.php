<?php

namespace App\core;

use App\controllers\BaseController;

class Router
{
    protected static array $routes;
    public static string $action;

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
        $path = Request::getPath();
        $method = Request::getMethod();
        $callback = self::$routes[$method][$path] ?? false;
        if ($callback === false) {
            return self::undefinedRoute($callback);
        }
        if (is_string($callback)) {
            return self::renderView($callback);
        }
        self::setAction($callback[1]);
        self::applyMiddleware($callback[0]);

        return call_user_func($callback);
    }

    protected static function applyMiddleware($callback)
    {
        if (call_user_func([$callback, 'middleware']) !== true) {
            return call_user_func([$callback, 'middleware']);
        }
    }

    protected static function setAction($callback)
    {
        if (!empty($callback)) {
            self::$action = $callback;
        }
    }

    protected static function renderView(string $view)
    {
        if (is_string($view)) {
            return View::render($view);
        }
    }

    protected static function undefinedRoute($callback)
    {
        if ($callback === false) {
            return Response::json_response_error('route not found', 'failed', 404);
//            return View::render('utils/_404');
        }

    }

}
