<?php

$db = new Database();
$conn = $db->getConnection();

// Register validation is moved to home.php
$successMessage = isset($_SESSION['successMessage']) ? $_SESSION['successMessage'] : null;
$errorMessage = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : null;
unset($_SESSION['successMessage'], $_SESSION['errorMessage']);

try {
    $stmt = $conn->prepare("SELECT * FROM Departments");
    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM Programs");
    $stmt->execute();
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $departments = [];
    $programs = [];
    // Optionally log or handle the error
}


?>

<div id="registerModal"
    class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden animate-fadeIn"
    onclick="if(event.target === this) this.classList.add('hidden')">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-xl">
        <h2 class="text-3xl font-bold mb-6 text-center text-gray-800 tracking-wide">Register</h2>
        <?php if ($successMessage): ?>
        <div class="mb-4 p-3 bg-green-200 text-green-800 rounded"><?= htmlspecialchars($successMessage) ?></div>
        <?php elseif ($errorMessage): ?>
        <div class="mb-4 p-3 bg-red-200 text-red-800 rounded"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="context" value="register">
            <div class="flex gap-4">
                <input type="text" name="username"
                    class="w-1/2 border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                    placeholder="Username" required>
                <input type="password" name="password"
                    class="w-1/2 border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                    placeholder="Password" required>
            </div>
            <div class="flex gap-4">
                <input type="text" name="firstName"
                    class="w-1/3 border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                    placeholder="First Name" required>
                <input type="text" name="middleName"
                    class="w-1/3 border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                    placeholder="Middle Name (Optional)">
                <input type="text" name="lastName"
                    class="w-1/3 border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                    placeholder="Last Name" required>
            </div>
            <select id="roleSelect" name="role"
                class="w-full border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                required>
                <option value="admin" selected>Admin</option>
                <option value="faculty">Faculty</option>
            </select>
            <div id="departmentDiv" class="hidden">
                <label for="department" class="block mb-1 text-gray-700 font-semibold">Department</label>
                <select name="department" id="department"
                    class="w-full border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300">
                    <option value="" disabled selected>Select Department</option>
                    <?php foreach ($departments as $department): ?>
                    <option value="<?= htmlspecialchars($department['DepartmentID']) ?>">
                        <?= htmlspecialchars($department['DepartmentName']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="programDiv" class="hidden">
                <label for="program" class="block mb-1 text-gray-700 font-semibold">Program</label>
                <select name="program" id="program"
                    class="w-full border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300">
                    <option value="" disabled selected>Select Program</option>
                    <?php foreach ($programs as $program): ?>
                    <option value="<?= htmlspecialchars($program['ProgramID']) ?>">
                        <?= htmlspecialchars($program['ProgramName']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block mb-1 text-gray-700 font-semibold" for="profilePic">Profile Picture
                    (Optional)</label>
                <input type="file" name="profilePic" id="profilePic" accept="image/*"
                    class="w-full border border-gray-300 p-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300">
            </div>
            <div class="flex gap-4">
                <button type="submit"
                    class="w-1/2 bg-lapis-lazuli text-white p-3 rounded-lg hover:bg-blue-800 transition duration-300 font-semibold shadow-md">Register</button>
                <button type="button" onclick="document.getElementById('registerModal').classList.add('hidden')"
                    class="w-1/2 bg-gray-100 p-3 rounded-lg hover:bg-gray-200 transition duration-300">Cancel</button>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('roleSelect').addEventListener('change', function() {
    const departmentDiv = document.getElementById('departmentDiv');
    const programDiv = document.getElementById('programDiv');
    if (this.value === 'faculty') {
        departmentDiv.classList.remove('hidden');
        programDiv.classList.remove('hidden');
    } else {
        departmentDiv.classList.add('hidden');
        programDiv.classList.add('hidden');
    }
});
</script>
<script>
window.addEventListener('DOMContentLoaded', (event) => {
    const registerModal = document.getElementById('registerModal');
    const successMessage = <?= json_encode($successMessage) ?>;
    const errorMessage = <?= json_encode($errorMessage) ?>;
    if (successMessage || errorMessage) {
        registerModal.classList.remove('hidden');
    }
});
</script>
<style>
@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.animate-fadeIn {
    animation: fadeIn 0.5s ease-in-out;
}
</style>

<script>
document.getElementById('roleSelect').addEventListener('change', function() {
    const departmentDiv = document.getElementById('departmentDiv');
    const programDiv = document.getElementById('programDiv');
    if (this.value === 'faculty') {
        departmentDiv.classList.remove('hidden');
        programDiv.classList.remove('hidden');
    } else {
        departmentDiv.classList.add('hidden');
        programDiv.classList.add('hidden');
    }
});
</script>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.animate-fadeIn {
    animation: fadeIn 0.5s ease-in-out;
}
</style>