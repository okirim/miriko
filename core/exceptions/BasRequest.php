<?php


namespace App\core;


use Throwable;

class BasRequest extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function make(string $message = 'bad request', int $code = 400, Throwable $previous = null)
    {
        http_response_code($code);
        throw new Exception($message, $code);
    }

}