<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="/assets/img/sti_icon.jpg" type="image/x-icon">
    <title>STI Scheduler</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">

    <?php include __DIR__ . '../../partials/header.php'; ?>

    <!-- Hero -->
    <section class="bg-white py-12 text-center">
        <h1 class="text-4xl font-bold text-blue-700">About Us</h1>
        <p class="mt-2 text-gray-600">Built with PHP, MySQL & TailwindCSS</p>
    </section>

    <!-- Feature Cards -->
    <section class="max-w-6xl mx-auto px-4 py-8 grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
            <svg class="w-12 h-12 text-blue-700 mb-3" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path d="M9 12h6m-3 -3v6m9-6a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h2 class="text-lg font-semibold">Curriculum</h2>
            <p class="text-gray-600 text-sm">Manage academic programs & subjects.</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
            <svg class="w-12 h-12 text-yellow-400 mb-3" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path d="M4 6h16M4 12h8m-8 6h16" />
            </svg>
            <h2 class="text-lg font-semibold">Room Assignment</h2>
            <p class="text-gray-600 text-sm">Assign classes to appropriate rooms.</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
            <svg class="w-12 h-12 text-blue-400 mb-3" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path d="M5 13l4 4L19 7" />
            </svg>
            <h2 class="text-lg font-semibold">Teacher Load</h2>
            <p class="text-gray-600 text-sm">Track and manage faculty schedule.</p>
        </div>
    </section>

    <?php include __DIR__ . '../../partials/footer.php'; ?>

    <!-- Login Modal -->
    <?php include __DIR__ . '../../partials/modals/modal_login.php'; ?>

</body>

</html>