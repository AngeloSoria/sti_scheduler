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

            <!-- Content -->
            <div class="flex flex-col items-center justify-center h-full">
                <section class="bg-white py-12 text-center px-6 shadow-md rounded-lg">
                    <h1 class="text-4xl mb-4 font-bold text-lapis-lazuli-3">About Us</h1>
                    <p class="text-gray-600">Sched-flow is a web-based application designed to streamline the
                        scheduling process for educational institutions.</p>
                    <p class="text-gray-600">Our goal is to provide a user-friendly platform that simplifies the
                        management of academic programs, subjects, and faculty schedules.</p>
                    <p class="text-gray-600">With a focus on efficiency and ease of use, we aim to enhance the
                        scheduling experience for both administrators and educators.</p>
                </section>
            </div>

        </div>

        <?php include __DIR__ . '../../partials/footer.php'; ?>

        <!-- Login Modal -->
        <?php include __DIR__ . '../../partials/modals/modal_login.php'; ?>

    </div>



</body>

</html>