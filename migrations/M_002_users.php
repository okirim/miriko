<?php


namespace App\migrations;
use App\core\Migrations;

class M_002_users
{
    public function up()
    {
        $columns="id INT AUTO_INCREMENT PRIMARY KEY, 
          email VARCHAR(255) NOT NULL,
          username VARCHAR(255) NOT NULL,
          password VARCHAR(255) NOT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ";
        Migrations::createTable('users',$columns);
    }


    public function down()
    {
        Migrations::deleteTable('users');
    }

}