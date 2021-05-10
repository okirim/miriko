<?php

namespace App\models;

use App\core\Response;
use App\core\Rules;

class RegisterModel extends Model
{
    public string $username;
    public string $email;
    public string $password;
    public string $confirm_password;

    /**
     * RegisterModel constructor.
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $confirm_password
     */
    public function __construct(string $username, string $email, string $password, string $confirm_password)
    {
        parent::__construct('users');
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->confirm_password = $confirm_password;
    }


    public function isValidate(bool $update = false)
    {
        $validate = [
            [$this->username, 'required', 'invalid username '],
            [$this->email, 'required email unique:email|users', 'invalid email'],
            [$this->password, 'required string', 'invalid password '],
            [$this->confirm_password, 'required string match', 'password confirm is invalid', $this->password]
        ];
        if (Rules::Validator($validate, $update)) {
            $this->error_validation_message = Rules::Validator($validate, $update);
            return false;
        }
        return true;
    }

    public function comments()
    {
        return $this->hasMany('comments');
    }
}