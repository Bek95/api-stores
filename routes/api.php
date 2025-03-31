<?php

use App\Controllers\UserController;
use App\Core\Router;
use App\Controllers\StoreController;
use App\Middleware\AuthMiddleware;

// Définition des routes

// routes non protégées
Router::addRoute('POST', 'user/create', [UserController::class, 'register']);
Router::addRoute('POST', 'user/login', [UserController::class, 'login']);

Router::addRoute('GET', 'stores', [StoreController::class, 'getStores']);
Router::addRoute('GET', 'stores/{id}', [StoreController::class, 'getStore']);

// routes protégées par le middleware
Router::addRoute('POST', 'stores/create', [StoreController::class, 'createStore'], [AuthMiddleware::class]);
Router::addRoute('PUT', 'stores/edit/{id}', [StoreController::class, 'updateStore'], [AuthMiddleware::class]);
Router::addRoute('DELETE', 'stores/delete/{id}', [StoreController::class, 'deleteStore'], [AuthMiddleware::class]);
