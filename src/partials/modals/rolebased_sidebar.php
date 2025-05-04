<aside id="sidebar"
    class="w-64 bg-white p-4 shadow h-screen transition-width duration-300 ease-in-out overflow-hidden relative">
    <!-- Sidebar Toggle Button -->
    <section class="w-full flex items-center justify-end mb-6">
        <button id="sidebarToggleBtn" aria-label="Toggle Sidebar"
            class="p-1 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 hover:bg-blue-50 bg-transparent">
            <svg id="sidebarClosedIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-700 hidden">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
            </svg>
            <svg id="sidebarOpenedIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-700">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
            </svg>
        </button>
    </section>
    <nav id="sidebarContent" class="space-y-2">

        <?php if ($_SESSION['user']['role'] == 'faculty'): ?>
        <a href="dashboard?page=my_schedule"
            class="sidebar-link block px-4 py-2 hover:bg-blue-50 rounded flex gap-3 items-center justify-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700 flex-shrink-0" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
            </svg>
            <span class="sidebar-text">My Schedule</span>
        </a>
        <?php endif; ?>

        <?php if ($_SESSION['user']['role'] == 'admin'): ?>
        <a href="dashboard?page=curriculums"
            class="sidebar-link block px-4 py-2 hover:bg-blue-50 rounded flex gap-3 items-center justify-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700 flex-shrink-0" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
            </svg>
            <span class="sidebar-text">Curriculums</span>
        </a>
        <a href="dashboard?page=schedules"
            class="sidebar-link block px-4 py-2 hover:bg-blue-50 rounded flex gap-3 items-center justify-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700 flex-shrink-0" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
            </svg>
            <span class="sidebar-text">Schedules</span>
        </a>
        <a href="dashboard?page=rooms"
            class="sidebar-link block px-4 py-2 hover:bg-blue-50 rounded flex gap-3 items-center justify-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700 flex-shrink-0" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />
            </svg>
            <span class="sidebar-text">Rooms</span>
        </a>
        <div>
            <button id="usersAccordionBtn" aria-expanded="false"
                class="w-full flex items-center justify-between px-4 py-2 hover:bg-blue-50 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                onclick="const submenu = document.getElementById('usersSubmenu'); const expanded = this.getAttribute('aria-expanded') === 'true'; this.setAttribute('aria-expanded', !expanded); submenu.classList.toggle('hidden'); submenu.classList.toggle('block'); document.getElementById('usersAccordionIcon').classList.toggle('rotate-180');">
                <span class="flex gap-3 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700 flex-shrink-0" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />

                    </svg>
                    <span class="sidebar-text">Users</span>
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" id="usersAccordionIcon"
                    class="w-4 h-4 text-gray-700 transition-transform duration-200" fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" class="size-4">

                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />

                </svg>
            </button>
            <div id="usersSubmenu" class="hidden pl-6 mt-1 space-y-1">
                <a href="dashboard?page=users&type=faculty"
                    class="sidebar-link block px-2 py-2 hover:bg-blue-50 rounded flex gap-3 items-center justify-start text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">

                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />

                    </svg>
                    <span class="sidebar-text">Faculty</span>
                </a>
                <a href="dashboard?page=users&type=admin"
                    class="sidebar-link block px-2 py-2 hover:bg-blue-50 rounded flex gap-3 items-center justify-start text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">

                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />

                    </svg>
                    <span class="sidebar-text">Admin</span>
                </a>
            </div>

        </div>
        <?php endif; ?>


    </nav>

</aside>


<script>
(function() {

    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebarTexts = sidebar.querySelectorAll('.sidebar-text');
    const sidebarLinks = sidebar.querySelectorAll('.sidebar-link');
    const usersAccordionBtn = document.getElementById('usersAccordionBtn');
    const sidebarOpenedIcon = document.getElementById('sidebarOpenedIcon');
    const sidebarClosedIcon = document.getElementById('sidebarClosedIcon');
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('w-16');
        sidebar.classList.toggle('w-64');
        sidebar.classList.toggle('sidebar-closed');
        sidebarTexts.forEach(text => {

            if (sidebar.classList.contains('w-16')) {
                text.style.opacity = '0';
                text.style.transform = 'translateX(-10px)';
                setTimeout(() => {
                    text.style.display = 'none';
                }, 300);
            } else {
                text.style.display = 'inline';
                setTimeout(() => {
                    text.style.opacity = '1';
                    text.style.transform = 'translateX(0)';
                }, 10);
            }
        });
        sidebarLinks.forEach(link => {

            if (sidebar.classList.contains('sidebar-closed')) {
                link.classList.add('justify-center');
                link.classList.remove('justify-start');
                link.classList.remove('px-4');
                link.classList.add('px-0');
            } else {
                link.classList.remove('justify-center');
                link.classList.add('justify-start');
                link.classList.add('px-4');
                link.classList.remove('px-0');
            }
        });
        // Special handling for users accordion button

        if (sidebar.classList.contains('sidebar-closed')) {
            usersAccordionBtn.classList.add('justify-center');
            usersAccordionBtn.classList.remove('justify-between');
            usersAccordionBtn.classList.remove('px-4');
            usersAccordionBtn.classList.add('px-0'); // Hide the accordion arrow icon

            sidebarOpenedIcon.classList.add('hidden');
            sidebarClosedIcon.classList.remove('hidden');
        } else {
            usersAccordionBtn.classList.remove('justify-center');
            usersAccordionBtn.classList.add('justify-between');
            usersAccordionBtn.classList.add('px-4');
            usersAccordionBtn.classList.remove('px-0'); // Show the accordion arrow icon

            sidebarClosedIcon.classList.add('hidden');
            sidebarOpenedIcon.classList.remove('hidden');
        }
    });
})();
</script>

<style>
/* Additional styles to center icons when sidebar is closed */
.sidebar-closed .sidebar-link svg,
.sidebar-closed #usersAccordionBtn svg {
    margin: 0 auto;
    transition: margin 0.3s ease;
}

.sidebar-text {
    transition: opacity 0.3s ease, transform 0.3s ease;
    display: inline;
}
</style>