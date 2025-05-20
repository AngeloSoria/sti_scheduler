<section id="main-content" class="flex-1 p-6 overflow-x-auto">

    <?php
    $view = $_GET['view'] ?? null;
    if ($view) {
        // Role validation: if page is curriculums and role is not admin, redirect to home
        if ($_SESSION['user']['role'] !== 'admin') {
            header("Location: /");
            exit();
        }
        // Sanitize the page parameter to prevent directory traversal
        // $view = str_replace(['..', '/', '\\'], '', $view);
        // $partialPath = dirname(__DIR__) . 'partials' . DIRECTORY_SEPARATOR . 'dashboard_pages' . DIRECTORY_SEPARATOR . $_SESSION['user']['role'] . DIRECTORY_SEPARATOR . "$view.php";
        $partialPath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $_SESSION['user']['role'] . DIRECTORY_SEPARATOR . $view . '.php');

        if (file_exists($partialPath)) {
            include $partialPath;
        } else {
            var_dump($partialPath);
            include __DIR__ . '../../404.php';
        }
    } else {
        // Default content if no page parameter
        require_once __DIR__ . '/functions/func_users.php';
        require_once __DIR__ . '/functions/func_schedules.php';
        require_once __DIR__ . '/functions/func_programs.php';
        require_once __DIR__ . '/functions/func_curriculums.php';
        require_once __DIR__ . '/functions/func_rooms.php';

        function getUserCount()
        {
            $db = new Database();
            $conn = $db->getConnection();
            try {
                $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM users");
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['total'] ?? 0;
            } catch (PDOException $e) {
                return 0;
            }
        }

        function getScheduleCount()
        {
            $db = new Database();
            $conn = $db->getConnection();
            try {
                $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM schedules");
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['total'] ?? 0;
            } catch (PDOException $e) {
                return 0;
            }
        }

        function getProgramCount()
        {
            $db = new Database();
            $conn = $db->getConnection();
            try {
                $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM programs");
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['total'] ?? 0;
            } catch (PDOException $e) {
                return 0;
            }
        }

        function getCurriculumCount()
        {
            $db = new Database();
            $conn = $db->getConnection();
            try {
                $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM curriculums");
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['total'] ?? 0;
            } catch (PDOException $e) {
                return 0;
            }
        }

        function getRoomCount()
        {
            $db = new Database();
            $conn = $db->getConnection();
            try {
                $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM rooms");
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['total'] ?? 0;
            } catch (PDOException $e) {
                return 0;
            }
        }

        $userCount = getUserCount();
        $scheduleCount = getScheduleCount();
        $programCount = getProgramCount();
        $curriculumCount = getCurriculumCount();
        $roomCount = getRoomCount();
        ?>
        <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded shadow p-8 flex items-center space-x-6">
                <div class="bg-blue-100 p-6 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
                <div>
                    <p class="text-4xl font-semibold"><?php echo $userCount; ?></p>
                    <p class="text-gray-500 text-xl">Users</p>
                </div>
            </div>
            <div class="bg-white rounded shadow p-8 flex items-center space-x-6">
                <div class="bg-green-100 p-6 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v7.5m-6.75-6h2.25m-9 2.25h4.5m.002-2.25h.005v.006H12v-.006Zm-.001 4.5h.006v.006h-.006v-.005Zm-2.25.001h.005v.006H9.75v-.006Zm-2.25 0h.005v.005h-.006v-.005Zm6.75-2.247h.005v.005h-.005v-.005Zm0 2.247h.006v.006h-.006v-.006Zm2.25-2.248h.006V15H16.5v-.005Z" />
                    </svg>
                </div>
                <div>
                    <p class="text-4xl font-semibold"><?php echo $scheduleCount; ?></p>
                    <p class="text-gray-500 text-xl">Schedules</p>
                </div>
            </div>
            <div class="bg-white rounded shadow p-8 flex items-center space-x-6">
                <div class="bg-yellow-100 p-6 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-yellow-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                    </svg>
                </div>
                <div>
                    <p class="text-4xl font-semibold"><?php echo $programCount; ?></p>
                    <p class="text-gray-500 text-xl">Programs</p>
                </div>
            </div>
            <div class="bg-white rounded shadow p-8 flex items-center space-x-6">
                <div class="bg-purple-100 p-6 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-purple-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div>
                    <p class="text-4xl font-semibold"><?php echo $curriculumCount; ?></p>
                    <p class="text-gray-500 text-xl">Curriculums</p>
                </div>
            </div>
            <div class="bg-white rounded shadow p-8 flex items-center space-x-6">
                <div class="bg-red-100 p-6 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />
                    </svg>
                </div>
                <div>
                    <p class="text-4xl font-semibold"><?php echo $roomCount; ?></p>
                    <p class="text-gray-500 text-xl">Rooms</p>
                </div>
            </div>
        </section>
        <?php
    }
    ?>

</section>