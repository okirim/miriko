<?php


namespace App\core;


class Migrations
{
    protected string $primary='';
    protected string $column='';
    protected string $type='';
    protected ?string $auto_increment='';
    protected ?string $default='';
    protected ?string $defaultCurrentTimeStamp='';
    protected ?string $isNull='';

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

    public static function SQL_generate(array $columns)
    {
       return implode(',',$columns);
    }

    public static function column(string $name)
    {
       $instance=new Migrations;
       $instance->column=strtolower(trim($name));
       return $instance;
    }

    public function primary(bool $isPrimary=true)
    {
        if (!$isPrimary) {
            $this->primary = '';
            return $this;
        }
        $this->primary = 'PRIMARY KEY';
        return $this;
    }

    public function type(string $type)
    {
        $this->type = strtoupper(trim($type));
        return $this;
    }

    public function default(string $defaultValue)
    {
        $this->default = "DEFAULT $defaultValue";
        return $this;
    }
    public function defaultCurrentTimeStamp()
    {
        $this->defaultCurrentTimeStamp = 'DEFAULT CURRENT_TIMESTAMP';
        return $this;
    }

    public function isNull(bool $null = true)
    {
        if (!$null) {
            $this->isNull = 'NOT NULL';
            return $this;
        }
        $this->isNull = 'NULL';
        return $this;
    }

    public function authIncrement()
    {
        $this->auto_increment = 'AUTO_INCREMENT';
        return $this;
    }

    public function make(): string
    {

        return "$this->column $this->type $this->auto_increment $this->primary $this->default $this->isNull $this->defaultCurrentTimeStamp";

    }
}