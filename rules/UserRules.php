<?php

namespace App\rules;

use App\models\Model;
use App\models\User;

class UserRules
{
    public static function login(array $fields)
    {
        $email = $fields['email'];
        $password = $fields['password'];
        $rules = [
            [$email, 'required email', 'invalid email'],
            [$password, 'required string', 'invalid password '],
        ];

        if (!User::isValid($rules)) {
            return User::validation_error_response();
        }
        return true;
    }

    public static function register(array $fields)
    {
        $email = $fields['email'];
        $password = $fields['password'];
        $username = $fields['username'];
        $password_confirm = $fields['password_confirm'];
        $rules = [
            [$username, 'required', 'invalid username '],
            [$email, 'required email unique:email|users', 'invalid email'],
            [$password, 'required string', 'invalid password '],
            [$password_confirm, 'required string match', 'password confirm is invalid', $password]
        ];

        if (!User::isValid($rules)) {
            return User::validation_error_response();
        }
        return true;
    }
}