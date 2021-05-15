<?php


namespace App\core\exceptions;


use Throwable;

/**
 * Class Exception
 * @package App\core\exceptions
 */
class Exception extends \Exception
{
    /**
     * Exception constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param mixed $message
     * @param int $code
     * @param Throwable|null $previous
     * @throws Exception
     */
    public static function make($message, int $code, Throwable $previous = null)
    {
        http_response_code($code);
        throw new Exception($message, $code);
    }

}