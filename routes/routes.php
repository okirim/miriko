<?php

use App\controllers\AuthController;
use App\controllers\HomeController;


$app->router->get('/', [HomeController::class, 'index']);
$app->router->get('/contact', [HomeController::class, 'contact']);
$app->router->post('/login', [AuthController::class, 'login']);
$app->router->get('/login', [AuthController::class, 'loginPage']);
$app->router->get('/register', [AuthController::class, 'registerPage']);
$app->router->post('/register', [AuthController::class, 'register']);





