<?php

namespace App\core\rules;



use App\core\orm\Query;

abstract class Validate
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

        if (!empty($val)) {

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
        if (!empty($val)) {
            preg_match('/\bmax\b:\d+/', $val, $matchesMax);
            preg_match('/\d+/', $matchesMax[0], $matches);
            return strlen($val) >= $matches[0];
        }

    }

    public static function min_then($val)
    {
        if (!empty($val)) {
            preg_match('/\bmin\b:\d+/', $val, $matchesMin);
            preg_match('/\d+/', $matchesMin[0], $matches);
            return strlen($val) <= $matches[0];
        }
    }

    public static function unique($rules, $uniqueValue)
    {
        preg_match('/(?<=\|)[a-z]+/', $rules, $matchTables);
        preg_match('/[a-z]+(?=\|)/', $rules, $matchColumns);
        $result = Query::findOne($matchTables[0], ["$matchColumns[0]" => $uniqueValue]);
        if (!empty($result)) {
            return false;
        }
        return true;
    }

    public static function error_message($response)
    {
        if (is_string($response) || is_array($response)) {
            return $response;
        }
        return 'Validation Error';

    }

}