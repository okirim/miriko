<?php


namespace App\core;


class Migrations
{
    public static Database $database;

    /**
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        self::$database = $database;
    }


    public static function createTable(string $table, string $columns)
    {
        $sql = "CREATE TABLE $table($columns)ENGINE=INNODB;";
        self::$database->pdo->exec($sql);
        echo "$table CREATED" . PHP_EOL;
    }

    public static function deleteTable(string $table)
    {
        $sql = "DROP TABLE $table";
        self::$database->pdo->exec($sql);
        echo "$table DELETED" . PHP_EOL;
    }
}