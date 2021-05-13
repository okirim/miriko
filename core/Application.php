<?php

namespace App\core;

use App\core\orm\Query;

class Application
{
    public Router $router;
    public Request $request;
    public Database $database;
    public Migrations $statement;
    public Query $query;
    public static string $ROOT_DIR;
    public static Application $app;

    public function __construct()
    {
        self::$app=$this;
        self::$ROOT_DIR=dirname(__DIR__);
        $this->database=new Database();
        $this->statement=new Migrations();
        $this->query=new Query();
        $this->request=new Request();
        $this->router = new Router();
    }

    public function run()
    {
       echo $this->router::resolve();
    }
}