<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <!-- Header -->
    <header class="flex justify-between items-center bg-blue-700 text-white p-4">
        <div class="font-bold text-lg">STI Admin Dashboard</div>
        <div class="space-x-4">
            <span><?php echo $_SESSION['user']; ?></span>
            <a href="/auth/logout.php"
                class="bg-yellow-400 text-black px-3 py-1 rounded hover:bg-yellow-300">Logout</a>
        </div>
    </header>

    <!-- Sidebar + Main Content -->
    <div class="flex">
        <aside class="w-64 bg-white p-4 shadow h-screen">
            <nav class="space-y-2">
                <a href="dashboard.php" class="block px-4 py-2 bg-blue-100 rounded">Dashboard</a>
                <a href="curriculum.php" class="block px-4 py-2 hover:bg-blue-50 rounded">Curriculum</a>
            </nav>
        </aside>

        <main class="flex-1 p-6">
            <h1 class="text-2xl font-semibold">Welcome, Admin!</h1>
            <p class="text-gray-600 mt-2">Use the sidebar to navigate.</p>
        </main>
    </div>
</body>

</html>