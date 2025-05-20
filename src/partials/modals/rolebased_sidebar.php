<aside id="sidebar"
    class="z-10 w-64 bg-white p-4 shadow h-screen transition-width duration-300 ease-in-out overflow-hidden relative">
    <!-- Sidebar Toggle Button -->
    <section class="w-full flex items-center justify-end mb-6">
        <button id="sidebarToggleBtn" aria-label="Toggle Sidebar" title="Toggle Sidebar"
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
            <a href="dashboard?view=my_schedule"
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
            <a href="dashboard?view=schedules"
                class="sidebar-link block px-4 py-2 hover:bg-blue-50 rounded flex gap-3 items-center justify-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700 flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                </svg>
                <span class="sidebar-text">Schedules</span>
            </a>
            <a href="dashboard?view=curriculums"
                class="sidebar-link block px-4 py-2 hover:bg-blue-50 rounded flex gap-3 items-center justify-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700 flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
                <span class="sidebar-text">Curriculums</span>
            </a>
            <a href="dashboard?view=programs"
                class="sidebar-link block px-4 py-2 hover:bg-blue-50 rounded flex gap-3 items-center justify-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700 flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                </svg>
                <span class="sidebar-text">Programs</span>
            </a>
            <a href="dashboard?view=sections"
                class="sidebar-link block px-4 py-2 hover:bg-blue-50 rounded flex gap-3 items-center justify-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700 flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
                <span class="sidebar-text">Sections</span>
            </a>
            <a href="dashboard?view=departments"
                class="sidebar-link block px-4 py-2 hover:bg-blue-50 rounded flex gap-3 items-center justify-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700 flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />
                </svg>
                <span class="sidebar-text">Departments</span>
            </a>
            <a href="dashboard?view=rooms"
                class="sidebar-link block px-4 py-2 hover:bg-blue-50 rounded flex gap-3 items-center justify-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700 flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />
                </svg>
                <span class="sidebar-text">Rooms</span>
            </a>
            <a href="dashboard?view=users&type=faculty"
                class="sidebar-link block px-4 py-2 hover:bg-blue-50 rounded flex gap-3 items-center justify-start">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6 w-6 h-6 text-gray-700 flex-shrink-0">

                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />

                </svg>
                <span class="sidebar-text">Faculty</span>
            </a>
            <a href="dashboard?view=users&type=admin"
                class="sidebar-link block px-4 py-2 hover:bg-blue-50 rounded flex gap-3 items-center justify-start">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6 w-6 h-6 text-gray-700 flex-shrink-0">

                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />

                </svg>
                <span class="sidebar-text">Admin</span>
            </a>
            <a href="dashboard?view=school_year_semesters"
                class="sidebar-link block px-4 py-2 hover:bg-blue-50 rounded flex gap-3 items-center justify-start">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-gray-700 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 7.5h18M3 12h18M3 16.5h18M4.5 21h15a2.25 2.25 0 002.25-2.25v-13.5A2.25 2.25 0 0019.5 3h-15A2.25 2.25 0 002.25 5.25v13.5A2.25 2.25 0 004.5 21z" />
                </svg>
                <span class="sidebar-text">School Term</span>
            </a>
        <?php endif; ?>


    </nav>

</aside>


<script>
    (function () {

        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const sidebarTexts = sidebar.querySelectorAll('.sidebar-text');
        const sidebarLinks = sidebar.querySelectorAll('.sidebar-link');
        const usersAccordionBtn = document.getElementById('usersAccordionBtn');
        const sidebarOpenedIcon = document.getElementById('sidebarOpenedIcon');
        const sidebarClosedIcon = document.getElementById('sidebarClosedIcon');

        // Function to apply sidebar state
        function applySidebarState(isClosed) {
            if (isClosed) {
                sidebar.classList.add('w-16', 'sidebar-closed');
                sidebar.classList.remove('w-64');
                sidebarTexts.forEach(text => {
                    text.style.opacity = '0';
                    text.style.transform = 'translateX(-10px)';
                    setTimeout(() => {
                        text.style.display = 'none';
                    }, 300);
                });
                sidebarLinks.forEach(link => {
                    link.classList.add('justify-center');
                    link.classList.remove('justify-start');
                    link.classList.remove('px-4');
                    link.classList.add('px-0');
                });
                sidebarOpenedIcon.classList.add('hidden');
                sidebarClosedIcon.classList.remove('hidden');
                if (usersAccordionBtn) {
                    usersAccordionBtn.classList.add('justify-center');
                    usersAccordionBtn.classList.remove('justify-between');
                    usersAccordionBtn.classList.remove('px-4');
                    usersAccordionBtn.classList.add('px-0'); // Hide the accordion arrow icon

                    // Close the accordion submenu when sidebar is closed
                    const usersSubmenu = document.getElementById('usersSubmenu');
                    if (usersSubmenu && !usersSubmenu.classList.contains('hidden')) {
                        usersSubmenu.classList.add('hidden');
                        usersSubmenu.classList.remove('block');
                        usersAccordionBtn.setAttribute('aria-expanded', 'false');
                        document.getElementById('usersAccordionIcon').classList.remove('rotate-180');
                    }
                }
            } else {
                sidebar.classList.remove('w-16', 'sidebar-closed');
                sidebar.classList.add('w-64');
                sidebarTexts.forEach(text => {
                    text.style.display = 'inline';
                    setTimeout(() => {
                        text.style.opacity = '1';
                        text.style.transform = 'translateX(0)';
                    }, 10);
                });
                sidebarLinks.forEach(link => {
                    link.classList.remove('justify-center');
                    link.classList.add('justify-start');
                    link.classList.add('px-4');
                    link.classList.remove('px-0');
                });
                sidebarClosedIcon.classList.add('hidden');
                sidebarOpenedIcon.classList.remove('hidden');
                if (usersAccordionBtn) {
                    usersAccordionBtn.classList.remove('justify-center');
                    usersAccordionBtn.classList.add('justify-between');
                    usersAccordionBtn.classList.add('px-4');
                    usersAccordionBtn.classList.remove('px-0'); // Show the accordion arrow icon
                }
            }
        }

        // Load sidebar state from localStorage
        const sidebarClosed = localStorage.getItem('sidebarClosed') === 'true';
        applySidebarState(sidebarClosed);

        toggleBtn.addEventListener('click', () => {
            const isClosed = sidebar.classList.contains('sidebar-closed');
            applySidebarState(!isClosed);
            // Save the new state to localStorage
            localStorage.setItem('sidebarClosed', !isClosed);
        });
    })();

    // document.getElementById('usersAccordionBtn').addEventListener('click', function () {
    //     const submenu = document.getElementById('usersSubmenu');
    //     const expanded = this.getAttribute('aria-expanded') === 'true';
    //     this.setAttribute('aria-expanded', !expanded);
    //     submenu.classList.toggle('hidden');
    //     submenu.classList.toggle('block');
    //     document.getElementById('usersAccordionIcon').classList.toggle('rotate-180');

    //     if (!expanded) {
    //         const sidebar = document.getElementById('sidebar');
    //         const sidebarClosed = sidebar.classList.contains('sidebar-closed');
    //         if (sidebarClosed) {
    //             sidebar.classList.remove('w-16', 'sidebar-closed');
    //             sidebar.classList.add('w-64');
    //             const sidebarTexts = sidebar.querySelectorAll('.sidebar-text');
    //             const sidebarLinks = sidebar.querySelectorAll('.sidebar-link');
    //             sidebarTexts.forEach(text => {
    //                 text.style.display = 'inline';
    //                 setTimeout(() => {
    //                     text.style.opacity = '1';
    //                     text.style.transform = 'translateX(0)';
    //                 }, 10);
    //             });
    //             sidebarLinks.forEach(link => {
    //                 link.classList.remove('justify-center');
    //                 link.classList.add('justify-start');
    //                 link.classList.add('px-4');
    //                 link.classList.remove('px-0');
    //             });
    //             document.getElementById('sidebarClosedIcon').classList.add('hidden');
    //             document.getElementById('sidebarOpenedIcon').classList.remove('hidden');
    //             const usersAccordionBtn = document.getElementById('usersAccordionBtn');
    //             usersAccordionBtn.classList.remove('justify-center');
    //             usersAccordionBtn.classList.add('justify-between');
    //             usersAccordionBtn.classList.add('px-4');
    //             usersAccordionBtn.classList.remove('px-0');
    //             localStorage.setItem('sidebarClosed', false);
    //         }
    //     }
    // });
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

    /* Make sidebar full width on mobile screens */
    @media (max-width: 640px) {
        #sidebar:not(.sidebar-closed) {
            width: 100vw !important;
            max-width: 100vw !important;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh !important;
            z-index: 9999 !important;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
    }
</style>