<?php
session_start();

require_once __DIR__ . '../../auth/login.php';
?>
<!DOCTYPE html>
<html lang="en">

<?php include_once __DIR__ . '../../partials/head.php'; ?>

<body class="bg-gray-100 font-sans">

    <div class="h-screen flex flex-col">

        <div class="flex-grow">
            <?php include __DIR__ . '../../partials/header.php'; ?>

            <!-- Hero -->
            <section class="bg-white py-12 text-center">
                <h1 class="text-4xl font-bold text-lapis-lazuli-3">Welcome to Sched-flow</h1>
                <p class="mt-2 text-gray-600">A simple and efficient scheduling system designed to manage courses,
                    faculty assignments, and room allocations with ease.</p>
            </section>

            <!-- Feature Cards -->
            <section class="max-w-6xl mx-auto px-4 py-8 grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    class="bg-white rounded-xl shadow-md p-6 hover:bg-blue-100 hover:shadow-xl hover:scale-105 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-blue-700 mb-3" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    <h2 class="text-lg font-semibold">Curriculum Management</h2>
                    <p class="text-gray-600 text-sm">Streamline the organization of academic subjects and programs.</p>
                </div>
                <div
                    class="bg-white rounded-xl shadow-md p-6 hover:bg-yellow-100 hover:shadow-xl hover:scale-105 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-yellow-400 mb-3" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                    <h2 class="text-lg font-semibold">Room Assignment</h2>
                    <p class="text-gray-600 text-sm">Easily allocate rooms to classes and activities to avoid conflicts.
                    </p>
                </div>
                <div
                    class="bg-white rounded-xl shadow-md p-6 hover:bg-blue-100 hover:shadow-xl hover:scale-105 transition-all">
                    <svg class="w-12 h-12 text-blue-400 mb-3" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7" />
                    </svg>
                    <h2 class="text-lg font-semibold">Faculty Scheduling</h2>
                    <p class="text-gray-600 text-sm">Efficiently manage faculty schedules and assignments.</p>
                </div>
            </section>
        </div>

        <?php include __DIR__ . '../../partials/footer.php'; ?>
        <!-- <?php // include __DIR__ . '../../temp/modal_register.php'; ?> -->

    </div>

</body>

</html>