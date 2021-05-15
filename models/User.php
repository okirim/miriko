<?php

namespace App\models;

use App\core\exceptions\Exception;
use App\core\Request;
use App\core\Response;
use App\core\Rules;

class User extends Model
{
    public function __construct()
    {
        parent::__construct('users');
    }

    public static function guest()
    {

    }

//    public ?string $username;
//    public ?string $email;
//    public ?string $password;
//
//    /**
//     * User constructor.
//     * @param string $username
//     * @param string $email
//     * @param string $password
//     */
//    public function __construct(?string $username, ?string $email, ?string $password)
//    {
//        parent::__construct('users');
//        $this->username = $username;
//        $this->email = $email;
//        $this->password = $password;
//    }
}