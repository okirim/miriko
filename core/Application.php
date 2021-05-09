<?php

namespace App\core;

class Application
{
    public Router $router;
    public Request $request;
    public Database $database;
    public Migrations $statement;
    public Query $query;
    public static string $ROOT_DIR;
    public static Application $app;

    /**
     * Application constructor.
     * @param string $rootPath
     * @param array $config
     */
    public function __construct(string $rootPath,array $config)
    {
        self::$app=$this;
        self::$ROOT_DIR=$rootPath;
        $this->database=new Database($config['database']);
        $this->statement=new Migrations($this->database);
        $this->query=new Query($this->database);
        $this->request=new Request();
        $this->router = new Router($this->request);
    }

    public function run()
    {
       echo $this->router->resolve();
    }
}