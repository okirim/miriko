<?php
use App\core\Application;

require_once __DIR__ . '/../vendor/autoload.php';

//.env (adding dotenv package)
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$app = new Application();

require_once __DIR__.'/../routes/routes.php';

$app->run();




//RoutesHandling::getInstance()->setRoutes($routes)->run();
//
//$routes=RoutesHandling::getInstance()->getRoutes();
//echo '<pre>';
//var_dump($routes);
//echo '</pre>';