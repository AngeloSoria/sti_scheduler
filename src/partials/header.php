<?php


$isLoggedIn = isset($_SESSION['user']);

if ($isLoggedIn) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['context']) && $_POST['context'] === 'logout') {
            session_destroy();
            header('Location: /');
            exit();
        }
    }

    require_once __DIR__ . '../../functions/GetProfileInfo.php';
    $getProfileInfo = new GetProfileInfo($conn);
    $userProfile = $getProfileInfo->getProfileInfo($_SESSION['user']['id']);
}

?>

<!-- Internal CSS for hover enhancement -->
<style>
    .hover-fb:hover {
        background-color: #f0f2f5;
    }

    /* Ensures the button text is vertically and horizontally centered */
    .button-style {
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 0.5rem 1rem;
        /* Adjust padding for consistency */
        font-size: 0.875rem;
        /* Same font size for all buttons */
    }

    .button-mobile-style {
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 0.5rem 1rem;
        /* Same padding as the desktop view */
        font-size: 1rem;
        /* Adjust font size for mobile to make it more readable */
        width: 100%;
        /* Full width for buttons to be consistent */
        margin: 0.5rem 0;
        /* Space between buttons */
        border-radius: 0.375rem;
        transition: background-color 0.2s ease;
    }

    .button-style,
    .button-mobile-style {
        border-radius: 0.375rem;
        transition: background-color 0.2s ease;
    }

    .button-style:hover,
    .button-mobile-style:hover {
        background-color: #f0f2f5;
        /* Same hover effect for consistency */
    }
</style>

<header class="bg-white text-gray-800 shadow-md z-20 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 py-2 flex justify-between items-center">

        <!-- Left: Logo and Title -->
        <a href="<?php echo $isLoggedIn ? '/dashboard' : '/'; ?>" class="flex items-center space-x-2">
            <img src="/assets/img/STI_LOGO_for_eLMS.png" alt="STI Logo" class="w-auto h-10">
            <span class="text-lg font-bold text-blue-900">Sched-flow</span>
        </a>

        <!-- Right: Navigation or Profile -->
        <?php if (!$isLoggedIn): ?>
            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="/about" class="button-style bg-white hover:bg-gray-200 text-black transition">About</a>
                <button onclick="document.getElementById('loginModal').classList.remove('hidden')"
                    class="button-style bg-yellow-400 hover:bg-yellow-300 text-black">
                    Login
                </button>
                <?php if (isset($_ENV['SHOW_DEV_TOOLS']) && $_ENV['SHOW_DEV_TOOLS'] == 'true'): ?>
                    <button onclick="document.getElementById('registerModal').classList.remove('hidden')" class="button-style bg-yellow-400 hover:bg-yellow-300 text-black">
                        Register
                    </button>
                <?php endif; ?>
            </div>

            <!-- Hamburger Icon -->
            <div class="md:hidden">
                <button id="menu-btn" class="focus:outline-none">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

        <?php else: ?>
            <div class="relative group">
                <button class="flex items-center space-x-2 focus:outline-none">
                    <img src="<?php echo !empty($userProfile['ProfilePic']) ? $userProfile['ProfilePic'] : '/assets/img/default-profile.png'; ?>"
                        alt="Profile" class="w-9 h-9 rounded-full border-2 border-blue-500">
                    <span class="hidden sm:inline font-medium text-sm">
                        <?php echo htmlspecialchars($_SESSION['user']['username']); ?>
                        <?php echo '(' . htmlspecialchars($_SESSION['user']['role']) . ')'; ?>
                    </span>
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <!-- Dropdown -->
                <div
                    class="absolute right-0 mt-2 bg-white text-black rounded-md shadow-lg w-44 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-150 z-50">
                    <a href="/profile" class="block px-4 py-2 text-sm hover-fb rounded-t">View
                        Profile</a>
                    <form method="post">
                        <input type="hidden" name="context" value="logout">
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm hover-fb rounded-b">Logout</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($_ENV['SHOW_DEV_TOOLS']) && $_ENV['SHOW_DEV_TOOLS'] == 'true'): ?>
        <?php include_once __DIR__ . '/modals/modal_register.php'; ?>
    <?php endif; ?>

    <!-- Mobile Nav for Public -->
    <?php if (!$isLoggedIn): ?>
        <nav id="mobile-menu" class="hidden md:hidden px-4 pb-4">
            <a href="/about" class="button-mobile-style bg-white hover:bg-gray-200 text-black">About</a>
            <button onclick="document.getElementById('loginModal').classList.remove('hidden')"
                class="button-mobile-style bg-yellow-400 text-black hover:bg-yellow-300">Login</button>
            <?php if (isset($_ENV['SHOW_DEV_TOOLS']) && $_ENV['SHOW_DEV_TOOLS'] == 'true'): ?>
                <button onclick="document.getElementById('registerModal').classList.remove('hidden')"
                    class="button-mobile-style bg-yellow-400 text-black hover:bg-yellow-300">Register</button>
            <?php endif; ?>
        </nav>

        <!-- Toggle Script -->
        <script>
            const btn = document.getElementById("menu-btn");
            const menu = document.getElementById("mobile-menu");

            btn?.addEventListener("click", () => {
                menu.classList.toggle("hidden");
            });
        </script>
    <?php endif; ?>
</header>