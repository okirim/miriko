<?php

namespace App\core;

class Router
{
    protected array $routes;
    public Request $request;

    /**
     * Router constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

    }


    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) {
            Response::setStatusCode(404);
            return View::render('utils/_404');
        }
        if (is_string($callback)) {
            return View::render($callback);
        }
        return call_user_func($callback);

    }
}
