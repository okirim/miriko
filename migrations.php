<?php


require_once __DIR__ . '/vendor/autoload.php';

use App\core\Application;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

$dotenv->load();
$config = [
    'orm' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ],
];
$app = new Application();

$app->database->migrate();

