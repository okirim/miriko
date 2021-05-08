<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\core\Application;

require_once __DIR__.'/../providers/provider.php';

require_once __DIR__.'/../config/config.php';

$app = new Application(dirname(__DIR__),$config);

require_once __DIR__.'/../routes/routes.php';

$app->run();




//RoutesHandling::getInstance()->setRoutes($routes)->run();
//
//$routes=RoutesHandling::getInstance()->getRoutes();
//echo '<pre>';
//var_dump($routes);
//echo '</pre>';