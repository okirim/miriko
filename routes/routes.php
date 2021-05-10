<?php

use App\controllers\AuthController;
use App\controllers\HomeController;
use App\core\Router;

Router::get('/', [HomeController::class, 'index']);
Router::get('/contact', [HomeController::class, 'contact']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/login', [AuthController::class, 'loginPage']);
Router::get('/register', [AuthController::class, 'registerPage']);
Router::post('/register', [AuthController::class, 'register']);
//Router::patch('/register', [AuthController::class, 'register']);






