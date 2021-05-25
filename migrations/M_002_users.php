<?php


namespace App\migrations;

use App\core\Migrations;

class M_002_users
{
    public static function up()
    {

        $id = Migrations::column('id')->type('int')->authIncrement()->primary()->make();
        $email = Migrations::column('email')->type('varchar(255)')->isNull(false)->make();
        $username = Migrations::column('username')->type('varchar(255)')->isNull(false)->make();
        $password = Migrations::column('password')->type('varchar(255)')->isNull(false)->make();
        $created_at = Migrations::column('created_at')->type('TIMESTAMP')->defaultCurrentTimeStamp()->make();
        $updated_at = Migrations::column('updated_at')->type('TIMESTAMP')->isNull()->make();
        $columns = [
            $id, $email,$username, $password, $created_at, $updated_at
        ];
        $sql = Migrations::SQL_generate($columns);
        Migrations::createTable('users', $sql);

    }


    public function down()
    {
        Migrations::deleteTable('users');
    }

}