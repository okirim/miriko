<?php


namespace App\core\exceptions;


use Throwable;

class UnAuthorizedException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    public static function make(string $message = 'unauthorized', int $code = 401, Throwable $previous = null)
    {
        http_response_code($code);
        throw new Exception($message, $code);
    }
}