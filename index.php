<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/router.php';
require __DIR__ . '/config/DbConnection.php';

$conn = new Database();
$conn = $conn->getConnection();

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router = new Router();

$router->add('/', function () {
    require __DIR__ . '/src/views/home.php';
});
$router->add('/about', function () {
    require __DIR__ . '/src/views/about.php';
});
$router->add('/contact', function () {
    require __DIR__ . '/src/views/contact.php';
});

// logged in
$router->add('/dashboard', function () {
    // Pass the page query parameter to dashboard.php
    $page = $_GET['page'] ?? null;
    require __DIR__ . '/src/views/dashboard.php';
});
// Removed the /dashboard/admin/curriculums route to avoid direct access

$router->dispatch($path);