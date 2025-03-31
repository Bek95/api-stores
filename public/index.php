<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use Dotenv\Dotenv;

//chargement des variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

//initialisation de la clé secrète de l'API
\App\Utils\JWT::init();

// Charger les routes depuis routes/api.php
require_once __DIR__ . '/../routes/api.php';

// Lancer le routeur
Router::dispatch();

