<?php

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
            $view = $_GET['view'] ?? null;
            if ($view) {
                // Role validation: if page is curriculums and role is not admin, redirect to home
                if ($view === 'curriculums' && $_SESSION['user']['role'] !== 'admin') {
                    header("Location: /");
                    exit();
                }
                // Sanitize the page parameter to prevent directory traversal
                // $view = str_replace(['..', '/', '\\'], '', $view);
                $partialPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'dashboard_pages' . DIRECTORY_SEPARATOR . $_SESSION['user']['role'] . DIRECTORY_SEPARATOR . "$view.php";
                if (file_exists($partialPath)) {
                    include $partialPath;
                } else {
                    include __DIR__ . '../../partials/dashboard_pages/404.php';
                }
            } else {
                // Default content if no page parameter
                // Placeholder counts - replace with actual data fetching logic
                $userCount = 120;
                $scheduleCount = 45;
                $programCount = 8;
                $curriculumCount = 15;
                $roomCount = 10;
                ?>
            <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <div class="bg-white rounded shadow p-4 flex items-center space-x-4">
                    <div class="bg-blue-100 p-3 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.75 6.879 2.034M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v7" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold"><?php echo $userCount; ?></p>
                        <p class="text-gray-500">Users</p>
                    </div>
                </div>
                <div class="bg-white rounded shadow p-4 flex items-center space-x-4">
                    <div class="bg-green-100 p-3 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2v-5H3v5a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold"><?php echo $scheduleCount; ?></p>
                        <p class="text-gray-500">Schedules</p>
                    </div>
                </div>
                <div class="bg-white rounded shadow p-4 flex items-center space-x-4">
                    <div class="bg-yellow-100 p-3 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.4 18.4a9 9 0 11-16.8 0" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold"><?php echo $programCount; ?></p>
                        <p class="text-gray-500">Programs</p>
                    </div>
                </div>
                <div class="bg-white rounded shadow p-4 flex items-center space-x-4">
                    <div class="bg-purple-100 p-3 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h3" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 12v-2a4 4 0 014-4h3" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold"><?php echo $curriculumCount; ?></p>
                        <p class="text-gray-500">Curriculums</p>
                    </div>
                </div>
                <div class="bg-white rounded shadow p-4 flex items-center space-x-4">
                    <div class="bg-red-100 p-3 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M4 6h16M4 18h16" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold"><?php echo $roomCount; ?></p>
                        <p class="text-gray-500">Rooms</p>
                    </div>
                </div>
            </section>
            <?php
            }
            ?>

        </main>

    </div>

    <!-- Footer -->
    <?php include_once __DIR__ . '../../partials/footer.php'; ?>
</body>

</html>
</create_file>