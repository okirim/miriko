<?php

namespace App\core;

class Router
{
    protected static array $routes;
    protected static array $params;
    protected static string $action;

    public static function get($path, $callback)
    {
        try {
            $parsed_path = self::extractParamsAndParsePath($path);
        } catch (\Exception $err) {
            throw $err;
        }
        self::$routes['get'][$parsed_path] = $callback;
    }

    public static function post($path, $callback)
    {
        try {
            $parsed_path = self::extractParamsAndParsePath($path);
        } catch (\Exception $err) {
            throw $err;
        }

        self::$routes['post'][$parsed_path] = $callback;
    }

    public static function patch($path, $callback)
    {
        try {
            $parsed_path = self::extractParamsAndParsePath($path);
        } catch (\Exception $err) {
            throw $err;
        }
        self::$routes['patch'][$parsed_path] = $callback;
    }

    public static function put($path, $callback)
    {
        try {
            $parsed_path = self::extractParamsAndParsePath($path);
        } catch (\Exception $err) {
            throw $err;
        }
        self::$routes['put'][$parsed_path] = $callback;
    }

    public static function delete($path, $callback)
    {
        try {
            $parsed_path = self::extractParamsAndParsePath($path);
        } catch (\Exception $err) {
            throw $err;
        }
        self::$routes['delete'][$parsed_path] = $callback;
    }


    public static function resolve()
    {
        try {
            $path = Request::getPath();
            $method = Request::getMethod();
            $callback = self::$routes[$method][$path] ?? false;
            if ($callback === false) {
                return self::undefinedRoute($callback);
            }
            if (is_string($callback)) {
                return self::renderView($callback);
            }
            //middleware
            self::setAction($callback[1]);
            self::applyMiddleware($callback[0]);

            if (!empty(self::$params)) {
                return call_user_func_array($callback, self::$params);
            }
            return call_user_func($callback);
        } catch (\Exception $err) {
            return Response::json_response_error($err->getMessage(), 'failed', 500);
        }
    }
   public static function getParams(){
        return self::$params;
}
public static function getAction(){
        return self::$action;
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

    protected static function extractParamsAndParsePath(string $path)
    {
        try {
            $parse_path = preg_replace('/(\{)[A-z 0-9]+(\})/', '[A-z 0-9 _ -]+', $path);
            $_parse_path = preg_replace('/(\/)/', '\/', $parse_path);
            $request_path = Request::getPath();
            $match_path = preg_match("/^$_parse_path$/", $request_path, $match);
            if ($match_path) {
                $path_arr = explode('/', $path);
                $request_path_arr = explode('/', $request_path);
                $params=array_diff($request_path_arr, $path_arr);
                self::$params = array_values($params);
                return $request_path;
            }
            return false;
        } catch (\Exception $err) {
            throw $err;
        }

    }

}
