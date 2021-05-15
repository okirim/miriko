<?php


namespace App\core;


use App\core\authentification\JWT;
use App\core\exceptions\Exception;
use App\models\User;


/**
 * Class Request
 * @package App\core
 */
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

                $_PATCH = json_decode(file_get_contents('php://input'));
                if (!is_array($_PATCH)) {
                    Response::json_response_error('invalid patch request');
                }

                foreach ($_PATCH as $key => $value) {
                    $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
            if (self::getMethod() === 'put') {

                $_PUT = json_decode(file_get_contents('php://input'));
                if (!is_array($_PUT)) {
                    Response::json_response_error('invalid put request');
                }
                foreach ($_PUT as $key => $value) {
                    $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
            if (self::getMethod() === 'delete') {
                $_DELETE = json_decode(file_get_contents('php://input'));
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
        return Router::$params;
    }

    public static function getQuery(string $query)
    {
        if (Request::getMethod() === 'get') {
            if (array_key_exists($query, $_GET)) {
                return $_GET[$query];
            }
        }
        return null;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function me()
    {
        try {
            try {
                $jwt = self::getTokenOrFail();
            } catch (\Exception $err) {
                Exception::make($err->getMessage(), $err->getCode());
            }
            $payload = JWT::validateOrFail($jwt);
            if (gettype($payload) === 'string' && empty($payload->user_id)) {
                Exception::make($payload, 401);
            }

            $user_id = $payload->user_id;
            return User::Olivine()::findUser($user_id);
        } catch (\Exception $err) {
            return Response::json_response_error($err->getMessage(), $err->getCode());
        }

    }

    public static function guest()
    {
        $jwt = self::getToken();
        $payload = JWT::validate($jwt);
        if ($payload === false) {
            return true;
        }
        if (!isset($payload->user_id)) {
            return true;
        }
        $user_id = $payload->user_id;
        if (empty($user_id)) {
            return true;
        }
        $user = User::Olivine()::findUser($user_id);
        if ($user) {
            return false;
        }
        return true;
    }

    public static function getHeader(string $header)
    {
        $headerKey = strtoupper(str_replace('-', '_', $header));
        return $_SERVER["HTTP_$headerKey"];
    }

    public static function getTokenOrFail()
    {
        try {
            if (!array_key_exists('HTTP_AUTHORIZATION', $_SERVER)) {
                Exception::make('UNAUTHORIZED NO BEARER TOKEN', 401);
            }
            $authorization_header = $_SERVER['HTTP_AUTHORIZATION'];

            $token_arr = explode(' ', $authorization_header);
            return $token_arr[1];
        } catch (\Exception $err) {
            Exception::make($err->getMessage(), $err->getCode());
        }
    }

    public static function getToken()
    {

        if (!array_key_exists('HTTP_AUTHORIZATION', $_SERVER)) {
            return false;
        }
        $authorization_header = $_SERVER['HTTP_AUTHORIZATION'];

        $token_arr = explode(' ', $authorization_header);
        return $token_arr[1];
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
