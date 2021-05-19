<?php


namespace App\migrations;


use App\core\Migrations;

class M_003_test
{
    public  function  up()
    {

        $id = Migrations::column('id')->type('int')->authIncrement()->primary()->make();
        $email = Migrations::column('email')->type('varchar(255)')->isNull(false)->make();
        $password = Migrations::column('password')->type('varchar(255)')->isNull(false)->make();
        $created_at = Migrations::column('created_at')->type('TIMESTAMP')->defaultCurrentTimeStamp()->make();
        $updated_at = Migrations::column('updated_at')->type('TIMESTAMP')->isNull()->make();
        $columns = [
            $id, $email, $password, $created_at, $updated_at
        ];
        $sql = Migrations::SQL_generate($columns);
        Migrations::createTable('test', $sql);
    }


    public function down()
    {
        Migrations::deleteTable('test');
    }
}

