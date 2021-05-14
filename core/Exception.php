<?php


namespace App\core;


use Throwable;

class Exception extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function make(string $message, int $code, Throwable $previous = null)
    {
        http_response_code($code);
        throw new Exception($message, $code);
    }

}