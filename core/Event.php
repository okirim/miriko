<?php

namespace App\core;

use App\listeners\AddEventListener;

class Event extends AddEventListener
{
    /**
     * @param string $name
     * @param ?mixed $argument
     */
    public static function Dispatch($name, $argument)
    {
        if (array_key_exists($name, static::$eventListener)) {
            if ($argument && is_array($argument)) {
                call_user_func_array(static::$eventListener[$name], $argument);
            } elseif ($argument && !is_array($argument)) {
                call_user_func(static::$eventListener[$name], $argument);
            } else {
                call_user_func(static::$eventListener[$name]);
            }
        }
    }
}