<?php


namespace App\core;


use App\models\User;

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

//                parse_str(file_get_contents('php://input'), $_PATCH);
                $_PATCH=json_decode(file_get_contents('php://input'));
                if (!is_array($_PATCH)) {
                    Response::json_response_error('invalid patch request');
                }

                foreach ($_PATCH as $key => $value) {
                    $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
            if (self::getMethod() === 'put') {

                $_PUT=json_decode(file_get_contents('php://input'));
                if (!is_array($_PUT)) {
                    Response::json_response_error('invalid put request');
                }
                foreach ($_PUT as $key => $value) {
                    $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
            if (self::getMethod() === 'delete') {
                $_DELETE=json_decode(file_get_contents('php://input'));
                if (!is_array($_DELETE)) {
                    Response::json_response_error('invalid put request');
                }
                foreach ($_DELETE as $key => $value) {
                    $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }

                if (!empty($body[$param])) {
                    return $body[$param];
                }
            if ($param) {
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

    public static function me()
    {
        $jwt = self::getToken();
        $payload = JWT::validate($jwt);
        $user_id = $payload->user_id;
        return User::Olivine()::findUser($user_id);
    }

    public static function getHeader(string $header)
    {
        $headerKey = strtoupper(str_replace('-', '_', $header));
        return $_SERVER["HTTP_$headerKey"];

    }

    public static function getToken()
    {
        try {
            $authorization_header = $_SERVER['HTTP_AUTHORIZATION'];
            $token_arr = explode(' ', $authorization_header);
            return $token_arr[1];
        } catch (\Exception $err) {
            Response::json_response_error($err->getMessage());
        }
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
