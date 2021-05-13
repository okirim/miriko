<?php


namespace App\core\rules;


use App\core\rules\Validate;

class Rules extends Validate
{

    public static function Validator(array $fields, bool $update = false)
    {
        foreach ($fields as $field) {

            if (preg_match('/(required)/', $field[1]) && $update===false) {
                $isValid = self::is_Required($field[0]);
                if (!$isValid) {
                    return self::error_message($field[2]);
                }
            }
            if(!empty($field[1])){
                if (preg_match('/(email)/', $field[1])) {
                    $isValid = self::is_Email($field[0]);
                    if (!$isValid) {
                        return self::error_message($field[2]);
                    }
                }
                if (preg_match('/(boolean)/', $field[1])) {
                    $isValid = self::is_Boolean($field[0]);
                    if (!$isValid) {
                        return self::error_message($field[2]);
                    }
                }
                if (preg_match('/(string)/', $field[1])) {
                    $isValid = self::is_String($field[0]);
                    if (!$isValid) {
                        return self::error_message($field[2]);
                    }
                }
                if (preg_match('/(numeric)/', $field[1])) {
                    $isValid = self::is_Numeric($field[0]);
                    if (!$isValid) {
                        return self::error_message($field[2]);
                    }
                }

                if (preg_match('/(match)/', $field[1])) {
                    $isValid = self::is_Match($field[0], $field[3]);
                    if (!$isValid) {
                        return self::error_message($field[2]);
                    }
                }
                if (preg_match('/(array)/', $field[1])) {
                    $isValid = self::is_Array($field[0]);
                    if (!$isValid) {
                        return self::error_message($field[2]);
                    }
                }
                if (preg_match('/(json)/', $field[1])) {
                    $isValid = self::is_Json($field[0]);
                    if (!$isValid) {
                        return self::error_message($field[2]);
                    }
                }
                if (preg_match('/(max)/', $field[1])) {
                    $isValid = self::max_then($field[0]);
                    if (!$isValid) {
                        return self::error_message($field[2]);
                    }
                }
                if (preg_match('/(min)/', $field[1])) {
                    $isValid = self::min_then($field[0]);
                    if (!$isValid) {
                        return self::error_message($field[2]);
                    }
                }
                if (preg_match('/(unique)/', $field[1])) {
                    $isValid = self::unique($field[1], $field[0]);
                    if (!$isValid) {
                        return self::error_message($field[2]);
                    }
                }
            }

        }
    }

}