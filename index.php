<?php
declare(strict_types=1);

ob_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/config/router.php';
require __DIR__ . '/config/dbConnection.php';

$conn = new Database();

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router = new Router();

$router->add('/', function () {
    require __DIR__ . '/src/views/home.php';
});
$router->add('/about', function () {
    require __DIR__ . '/src/views/about.php';
});

// logged in
$router->add('/dashboard', function () {
    session_start();

    $conn = new Database();
    $conn = $conn->getConnection();

    if (!isset($_SESSION['user'])) {
        header("Location: /");
        exit();
    }
    $view = $_GET['view'] ?? null;

    // Map of roles to pages they can access
    $roleValidationMap = [
        'admin' => [
            'schedules',
            'curriculums',
            'programs',
            'sections',
            'departments',
            'rooms',
            'users',
            'school_year_semesters'
        ],
        'faculty' => [
            'my_schedule'
        ],
    ];

    if ($view) {
        $userRole = $_SESSION['user']['role'];
        if (!isset($roleValidationMap[$userRole]) || !in_array($view, $roleValidationMap[$userRole])) {
            header("Location: /");
            exit();
        }
    }

    require __DIR__ . '/src/views/dashboard.php';
});
$router->add('/profile', function () {
    // Pass the page query parameter to dashboard.php
    $userid = $_GET['id'] ?? null;
    require __DIR__ . '/src/views/profile.php';
});

$router->dispatch($path);