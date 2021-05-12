<?php


namespace App\core;


class Request
{
    public static function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $questionMark = strpos($path, '?');
        if (!$questionMark) return $path;
        return substr($path, 0, $questionMark);
    }

    public static function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public static function getBody($param = null)
    {
        try {
            $body = [];
            if (self::getMethod() === 'get') {
                $_GET = json_decode(file_get_contents('php://input'), true);
                foreach ($_GET as $key => $value) {
                    $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
            if (self::getMethod() === 'post') {
                $_POST = json_decode(file_get_contents('php://input'), true);
                if (!empty($_POST)) {
                    foreach ($_POST as $key => $value) {
                        $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
            if (self::getMethod() === 'patch') {
                parse_str(file_get_contents('php://input'), $_PATCH);
                if (!is_array($_PATCH)) {
                    Response::json_response_error('invalid patch request');
                }
                foreach ($_PATCH as $key => $value) {
                    $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
            if (self::getMethod() === 'put') {
                parse_str(file_get_contents('php://input'), $_PUT);
                if (!is_array($_PUT)) {
                    Response::json_response_error('invalid put request');
                }
                foreach ($_PUT as $key => $value) {
                    $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
            if (self::getMethod() === 'delete') {
                parse_str(file_get_contents('php://input'), $_DELETE);
                if (!is_array($_DELETE)) {
                    Response::json_response_error('invalid put request');
                }
                foreach ($_DELETE as $key => $value) {
                    $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
            if ($param) {
                if (!empty($body[$param])) {
                    return $body[$param];
                }
                return Response::json_response_error("$param is empty");
            }
            return $body;
        } catch (\Exception $error) {
            return Response::json_response_error($error);
        }
    }

    public static function getParams()
    {

    }

    public static function getQuery()
    {

    }

    public static function Body($param = null)
    {
        $req = self::instanceRequest();
        return $req->getBody($param);
    }

    protected static function instanceRequest()
    {
        return new Request();
    }

}











//public function getPath()
//    {
//        $path = $_SERVER['REQUEST_URI'] ?? '/';
//        $questionMark = strpos($path, '?');
//        if (!$questionMark) return $path;
//        return substr($path, 0, $questionMark);
//    }
//
//    public function getMethod()
//    {
//        return strtolower($_SERVER['REQUEST_METHOD']);
//    }
