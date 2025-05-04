<?php

$isLoggedIn = isset($_SESSION['user']);

if ($isLoggedIn) {
    // Logout process
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['context']) && $_POST['context'] === 'logout') {
            session_destroy();
            header('Location: /');
            exit();
        }
    }
}
?>

<header class="bg-lapis-lazuli text-white shadow-lg z-5">
    <div class="max-w-7xl mx-auto px-4 py-2 flex justify-between items-center">

        <!-- Left Logo -->
        <a href="/">
            <div class="flex items-center space-x-2">
                <img src="/assets/img/STI_LOGO_for_eLMS.png" alt="STI Logo" width="80">
                <span class="text-lg font-semibold">STI Scheduler</span>
                <?php if ($isLoggedIn): ?>
                <p>| Dashboard</p>
                <?php endif; ?>
            </div>
        </a>


        <!-- Right Content (Login/Dropdown) -->
        <?php if (!$isLoggedIn): ?>
        <!-- Public Navbar -->
        <div class="flex items-center md:space-x-4">
            <button onclick="document.getElementById('loginModal').classList.remove('hidden')"
                class="hidden md:inline bg-yellow-400 text-black px-4 py-1 rounded hover:bg-yellow-300">Login</button>
            <a href="/about" class="hidden md:inline hover:underline">About</a>
            <?php if (isset($_ENV['DEVELOPMENT_MODE']) && $_ENV['DEVELOPMENT_MODE'] == 'true'): ?>
            <button onclick="document.getElementById('registerModal').classList.remove('hidden')"
                class="hidden md:inline bg-yellow-400 text-black px-4 py-1 rounded hover:bg-yellow-300">Register</button>
            <?php endif; ?>
            <!-- Hamburger for small screens -->
            <button id="menu-btn" class="md:hidden focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <?php else: ?>
        <!-- Authenticated Navbar -->
        <div class="relative group">
            <!-- Button -->
            <button class="flex items-center space-x-2 focus:outline-none">
                <img src="<?php echo $_SESSION['user']['profilepic']; ?>" alt="Profile"
                    class="w-8 h-8 rounded-full border-2 border-white">
                <span class="hidden sm:inline">
                    <?php echo htmlspecialchars($_SESSION['user']['username']); ?>
                    <?php echo '(' . htmlspecialchars($_SESSION['user']['role']) . ')'; ?>
                </span>
                <svg class="w-4 h-4" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown: must be inside the same parent (group) -->
            <div
                class="absolute right-0 mt-2 bg-white text-black rounded shadow-md w-40 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                <a href="<?php echo "/profile?id=" . $_SESSION['user']['id'] ?>"
                    class="block px-4 py-2 hover:bg-gray-100">View Profile</a>
                <form method="post">
                    <input type="hidden" name="context" value="logout">
                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
                </form>
            </div>
        </div>

        <?php endif; ?>
    </div>

    <?php if (!$isLoggedIn): ?>
    <!-- Mobile Nav for Public -->
    <nav id="mobile-menu" class="hidden md:hidden px-4 pb-4">
        <button onclick="document.getElementById('loginModal').classList.remove('hidden')"
            class="w-full bg-yellow-400 text-black px-4 py-2 rounded mt-2">Login</button>
        <a href="/about" class="block mt-2 hover:bg-blue-500 w-full px-4 py-2 rounded text-center">About</a>
    </nav>
    <?php endif; ?>
</header>

<style>
#mobile-menu {
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transition: max-height 0.3s ease, opacity 0.3s ease;
    display: none;
}

#mobile-menu.open {
    opacity: 1;
    display: block;
}
</style>

<style>
@media (max-width: 640px) {
    header div.flex.items-center.space-x-2 img {
        width: 50px !important;
    }

    header div.flex.items-center.space-x-2 span {
        font-size: 0.75rem;
        /* text-sm */
    }

    header div.flex.items-center.space-x-2 p {
        font-size: 0.75rem;
        /* text-sm */
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', function() {
            if (mobileMenu.classList.contains('open')) {
                // Close menu with transition
                mobileMenu.style.maxHeight = mobileMenu.scrollHeight + "px";

                // Force reflow to apply the height before collapsing
                mobileMenu.offsetHeight;

                mobileMenu.style.maxHeight = "0px";

                // transition end event to remove class
                mobileMenu.addEventListener('transitionend', function() {
                    mobileMenu.classList.remove('open');
                    mobileMenu.classList.add('hidden');
                }, {
                    once: true
                });

            } else {
                // Open menu
                mobileMenu.classList.remove('hidden');
                mobileMenu.classList.add('open');
                mobileMenu.style.maxHeight = mobileMenu.scrollHeight + "px";

            }
            // Optionally toggle aria-expanded for accessibility
            const expanded = menuBtn.getAttribute('aria-expanded') === 'true';
            menuBtn.setAttribute('aria-expanded', !expanded);
        });
    }
});
</script>