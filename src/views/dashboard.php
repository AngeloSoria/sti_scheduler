<?php

require_once __DIR__ . "/../../config/dbConnection.php";
$db = new Database();
$conn = $db->getConnection();

$userRole = $_SESSION['user']['role'] ?? '';

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
        
        <?php
        if ($userRole === 'faculty') {
            include_once __DIR__ . '../../partials/dashboard_pages/faculty/my_schedule.php';
        } elseif ($userRole === 'admin') {
            include_once __DIR__ . '../../partials/dashboard_pages/admin/admin_dashboard.php';
        } else {
            echo "Unauthorized access.";
        }
        ?>

    </div>

    <!-- Footer -->
    <?php include_once __DIR__ . '../../partials/footer.php'; ?>
</body>

</html>