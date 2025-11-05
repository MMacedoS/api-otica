<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

date_default_timezone_set('America/Sao_Paulo');

$router = require __DIR__ . '/src/Routers/web.php';

$router->init();
