<?php


namespace App\core;


class Rules
{
    public static function is_Email($val)
    {
        return filter_var($val, FILTER_VALIDATE_EMAIL);
    }

    public static function is_Boolean($val)
    {
        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    public static function is_String($val)
    {
        return is_string($val);
    }

    public static function is_Numeric($val)
    {
        return is_numeric($val);
    }

    public static function is_Required($val)
    {
        if (isset($val)) {
            return true;
        }
        return false;
    }

    public static function is_Match($val, $match)
    {

        if ($val === $match) {
            return true;
        }
        return false;
    }

    public static function is_Array($val)
    {
        if (self::is_Array($val)) {
            return true;
        }
        return false;
    }

    public static function is_Json($val)
    {
        if (self::is_Json($val)) {
            return true;
        }
        return false;
    }
    public static function max_then($val)
    {
        if(!empty($val)){
            preg_match('/\bmax\b:\d+/', $val, $matchesMax);
            preg_match('/\d+/', $matchesMax[0], $matches);
            return strlen($val) >= $matches[0];
        }

    }
    public static function min_then($val)
    {
      if(!empty($val)){
          preg_match('/\bmin\b:\d+/', $val, $matchesMin);
          preg_match('/\d+/', $matchesMin[0], $matches);
          return  strlen($val) <= $matches[0];
      }
    }

    public static function error_message($respnose)
    {
        if (is_string($respnose) || is_array($respnose)) {
            return $respnose;
        }
        return 'Validation Error';

    }

    public static function Validator($fields)
    {
        foreach ($fields as $field) {

            if (stripos($field[1], 'email')) {
                $isValid = self::is_Email($field[0]);
                if (!$isValid) {
                    return self::error_message($field[2]);
                }
            }
            if (stripos($field[1], 'boolean')) {
                $isValid = self::is_Boolean($field[0]);
                if (!$isValid) {
                    return self::error_message($field[2]);
                }
            }
            if (stripos($field[1], 'string')) {
                $isValid = self::is_String($field[0]);
                if (!$isValid) {
                    return self::error_message($field[2]);
                }
            }
            if (stripos($field[1], 'numeric')) {
                $isValid = self::is_Numeric($field[0]);
                if (!$isValid) {
                    return self::error_message($field[2]);
                }
            }
            if (stripos($field[1], 'required')) {
                $isValid = self::is_Required($field[0]);
                if (!$isValid) {
                    return self::error_message($field[2]);
                }
            }
            if (stripos($field[1], 'match')) {
                $isValid = self::is_Match($field[0], $field[3]);
                if (!$isValid) {
                    return self::error_message($field[2]);
                }
            }
            if (stripos($field[1], 'array')) {
                $isValid = self::is_Array($field[0]);
                if (!$isValid) {
                    return self::error_message($field[2]);
                }
            }
            if (stripos($field[1], 'json')) {
                $isValid = self::is_Json($field[0]);
                if (!$isValid) {
                    return self::error_message($field[2]);
                }
            }
            if (stripos($field[1], 'max')) {
                   $isValid=self::max_then($field[0]);
                if (!$isValid) {
                    return self::error_message($field[2]);
                }
            }
            if (stripos($field[1], 'min')) {
                $isValid = self::min_then($field[0]);
                if (!$isValid) {
                    return self::error_message($field[2]);
                }
            }

        }
    }

}