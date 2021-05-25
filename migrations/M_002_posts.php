<?php


namespace App\migrations;


use App\core\Migrations;

class M_002_posts
{
    public static  function up()
    {
        $columns = "id INT AUTO_INCREMENT PRIMARY KEY, 
          name VARCHAR(255) NOT NULL,
          price INT NOT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ";
        Migrations::createTable('posts', $columns);
    }


    public function down()
    {
        Migrations::deleteTable('posts');
    }
}