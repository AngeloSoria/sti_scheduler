<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>STI Scheduler</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">

    <?php include __DIR__ . '../../partials/header.php'; ?>

    <!-- Hero -->
    <section class="bg-white py-12 text-center">
        <h1 class="text-4xl font-bold text-blue-700">Welcome to STI Scheduling System</h1>
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
    <div id="loginModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold mb-4 text-center">Admin Login</h2>
            <form action="auth/login.php" method="POST" class="space-y-4">
                <input type="text" name="username" class="w-full border p-2 rounded" placeholder="Username" required>
                <input type="password" name="password" class="w-full border p-2 rounded" placeholder="Password"
                    required>
                <button type="submit" class="w-full bg-blue-700 text-white p-2 rounded hover:bg-blue-800">Login</button>
                <button type="button" onclick="document.getElementById('loginModal').classList.add('hidden')"
                    class="w-full mt-2 bg-gray-200 p-2 rounded">Cancel</button>
            </form>
        </div>
    </div>

</body>

</html>