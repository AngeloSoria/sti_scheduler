<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/dbConnection.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/router.php';
require_once __DIR__ . '/config/DbConnection.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router = new Router();

$router->add('/', function () {
    require __DIR__ . '/src/views/login.php';
});
$router->add('/about', function () {
    require __DIR__ . '/src/views/about.php';
});
$router->add('/contact', function () {
    require __DIR__ . '/src/views/contact.php';
});

$router->dispatch($path);

