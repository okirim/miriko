<?php


namespace App\core;


class Migrations
{

    public static function createTable(string $table, string $columns)
    {
        $sql = "CREATE TABLE $table($columns)ENGINE=INNODB;";
        Database::$pdo->exec($sql);
        echo "$table CREATED" . PHP_EOL;
    }

    public static function deleteTable(string $table)
    {
        $sql = "DROP TABLE $table";
        Database::$pdo->exec($sql);
        echo "$table DELETED" . PHP_EOL;
    }
}