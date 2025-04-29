<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
?>

<header class="bg-blue-700 text-white shadow">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">

        <!-- Left Logo -->
        <div class="flex items-center space-x-2">
            <img src="/sti-scheduler/assets/img/logo.png" alt="STI Logo" class="h-8">
            <span class="text-lg font-semibold">STI Scheduler</span>
        </div>

        <!-- Right Content (Login/Dropdown) -->
        <?php if (!$isLoggedIn): ?>
            <!-- Public Navbar -->
            <div class="flex items-center md:space-x-4">
                <button onclick="document.getElementById('loginModal').classList.remove('hidden')"
                    class="hidden md:inline bg-yellow-400 text-black px-4 py-1 rounded hover:bg-yellow-300">Login</button>
                <a href="/about" class="hidden md:inline hover:underline">About</a>
                <!-- Hamburger for small screens -->
                <button id="menu-btn" class="md:hidden focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

        <?php else: ?>
            <!-- Authenticated Navbar -->
            <!-- Authenticated Navbar -->
            <div class="relative group">
                <!-- Button -->
                <button class="flex items-center space-x-2 focus:outline-none">
                    <img src="/thesis-system/assets/img/profile.png" alt="Profile"
                        class="w-8 h-8 rounded-full border-2 border-white">
                    <span class="hidden sm:inline"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <svg class="w-4 h-4" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown: must be inside the same parent (group) -->
                <div
                    class="absolute right-0 mt-2 bg-white text-black rounded shadow-md w-40 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                    <a href="/thesis-system/admin/profile.php" class="block px-4 py-2 hover:bg-gray-100">View Profile</a>
                    <a href="/thesis-system/auth/logout.php" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <?php if (!$isLoggedIn): ?>
        <!-- Mobile Nav for Public -->
        <nav id="mobile-menu" class="hidden md:hidden px-4 pb-4">
            <button onclick="document.getElementById('loginModal').classList.remove('hidden')"
                class="w-full bg-yellow-400 text-black px-4 py-2 rounded mt-2">Login</button>
            <a href="#" class="block mt-2 hover:underline">About</a>
        </nav>

        <script>
            const btn = document.getElementById("menu-btn");
            const menu = document.getElementById("mobile-menu");
            btn?.addEventListener("click", () => menu.classList.toggle("hidden"));
        </script>
    <?php endif; ?>
</header>