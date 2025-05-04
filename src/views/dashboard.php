<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form is submitted for registration
    if (isset($_POST['context'])) {
        switch ($_POST['context']) {
            case 'logout':
                // Handle logout
                session_destroy();
                header('Location: /');
                exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<?php include_once __DIR__ . '../../partials/head.php'; ?>

<body class="bg-gray-100">

    <!-- Header -->
    <?php include_once __DIR__ . '../../partials/header.php'; ?>

    <!-- Sidebar + Main Content -->
    <div class="flex">
        <?php include_once __DIR__ . '../../partials/modals/rolebased_sidebar.php'; ?>

        <main id="main-content" class="flex-1 p-6 overflow-x-auto">

            <?php
            $page = $_GET['page'] ?? null;
            if ($page) {
                // Sanitize the page parameter to prevent directory traversal
                // $page = str_replace(['..', '/', '\\'], '', $page);
                $partialPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'dashboard_pages' . DIRECTORY_SEPARATOR . $_SESSION['user']['role'] . DIRECTORY_SEPARATOR . "$page.php";
                if (file_exists($partialPath)) {
                    include $partialPath;
                } else {
                    echo '<p>Page not found.</p>';
                }
            } else {
                // Default content if no page parameter
                echo '<p>Welcome to the dashboard!</p>';
            }
            ?>

        </main>


    </div>

    <!-- Footer -->
    <?php include_once __DIR__ . '../../partials/footer.php'; ?>
</body>

</html>