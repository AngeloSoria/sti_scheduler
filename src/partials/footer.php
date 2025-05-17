<?php
include __DIR__ . '../../partials/modals/modal_login.php';
// include __DIR__ . '../../temp/modal_register.php';
?>

<footer class="bg-[#26619C] text-white text-center py-4 mt-10">
    <div class="flex justify-between items-center px-4 md:px-20">
        <!-- Left Section -->
        <div class="text-sm">
            <p>&copy; <?php echo date("Y"); ?> Sched-flow. All rights reserved.</p>
        </div>

        <!-- Right Section (Social Media Links) -->
        <div class="flex space-x-4">
            <a href="#" target="_blank" class="social-circle-icon bg-white hover:bg-yellow-500">
                <i class="fab fa-facebook-f text-[#26619C] hover:text-white p-2"></i>
            </a>
            <a href="#" target="_blank" class="social-circle-icon bg-white hover:bg-yellow-500">
                <i class="fab fa-twitter text-[#26619C] hover:text-white p-2"></i>
            </a>
            <a href="#" target="_blank" class="social-circle-icon bg-white hover:bg-yellow-500">
                <i class="fab fa-instagram text-[#26619C] hover:text-white p-2"></i>
            </a>
            <a href="#" target="_blank" class="social-circle-icon bg-white hover:bg-yellow-500">
                <i class="fab fa-youtube text-[#26619C] hover:text-white p-2"></i>
            </a>
        </div>
    </div>
</footer>

<!-- Internal CSS for circular icons and hover effects -->
<style>
    .social-circle-icon {
        display: inline-block;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        text-align: center;
        line-height: 40px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .social-circle-icon i {
        font-size: 18px;
        transition: color 0.3s ease;
    }

    /* Hover effect to make the icon a bit bigger on hover */
    .social-circle-icon:hover {
        transform: scale(1.1);
    }
</style>