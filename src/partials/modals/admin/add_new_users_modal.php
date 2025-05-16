<?php

require_once __DIR__ . '/../../dashboard_pages/admin/functions/func_users.php';

if (isset($_GET['action']) && $_GET['action'] === 'getCurriculumSubjects' && isset($_GET['programId'])) {
    header('Content-Type: application/json');
    $programId = $_GET['programId'];

    // Validate programId
    if (empty($programId) || !is_numeric($programId)) {
        echo json_encode([]);
        exit;
    }
    $subjects = getCurriculumSubjectsByProgram($programId);
    echo json_encode($subjects);
    exit;
}

// Register validation is moved to home.php
$successMessage = isset($_SESSION['successMessage']) ? $_SESSION['successMessage'] : null;
$errorMessage = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : null;
unset($_SESSION['successMessage'], $_SESSION['errorMessage']);

$departments = getDepartments();
$programs = getPrograms();

?>

<div id="registerModal" tabindex="-1" aria-hidden="true"
    class="opacity-0 pointer-events-none fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black bg-opacity-50 transition-opacity duration-300 ease-in-out">
    <div class="relative w-full max-w-xl p-6 mx-auto bg-white rounded-lg shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">Register</h2>
            <button type="button" aria-label="Close modal"
                class="text-gray-400 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                onclick="closeRegisterModal()">
                <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
        </div>
        <?php if ($successMessage): ?>
            <div class="mb-4 p-3 bg-green-200 text-green-800 rounded"><?= htmlspecialchars($successMessage) ?></div>
        <?php elseif ($errorMessage): ?>
            <div class="mb-4 p-3 bg-red-200 text-red-800 rounded"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>
        <form method="POST" action="src/partials/dashboard_pages/admin/functions/func_users.php" id="addUserForm"
            enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="action" value="addUser">
            <input type="hidden" name="type" value="<?php echo htmlspecialchars($_GET['type'] ?? ''); ?>">
            <div class="flex gap-4">
                <div class="w-1/2">
                    <label for="addUsername" class="block mb-1 text-gray-700 font-semibold">Username</label>
                    <input type="text" name="addUsername" id="addUsername"
                        class="w-full border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                        placeholder="Username" required>
                </div>
                <div class="w-1/2">
                    <label for="addPassword" class="block mb-1 text-gray-700 font-semibold">Password</label>
                    <input type="password" name="addPassword" id="addPassword"
                        class="w-full border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                        placeholder="Password" required>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="w-1/3">
                    <label for="addFirstName" class="block mb-1 text-gray-700 font-semibold">First Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="addFirstName" id="addFirstName"
                        class="w-full border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                        placeholder="First Name" required>
                </div>
                <div class="w-1/3">
                    <label for="addMiddleName" class="block mb-1 text-gray-700 font-semibold">Middle Name</label>
                    <input type="text" name="addMiddleName" id="addMiddleName"
                        class="w-full border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                        placeholder="Middle Name (Optional)">
                </div>
                <div class="w-1/3">
                    <label for="addLastName" class="block mb-1 text-gray-700 font-semibold">Last Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="addLastName" id="addLastName"
                        class="w-full border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                        placeholder="Last Name" required>
                </div>
            </div>
            <div>
                <label for="addRoleSelect" class="block mb-1 text-gray-700 font-semibold">Role</label>
                <select id="addRoleSelect" name="addRoleSelect"
                    class="w-full border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                    required <?php if (isset($_GET['type']) && in_array($_GET['type'], ['admin', 'faculty']))
                        echo 'disabled'; ?>>
                    <option value="admin" <?php if (isset($_GET['type']) && $_GET['type'] === 'admin')
                        echo 'selected'; ?>>Admin</option>
                    <option value="faculty" <?php if (isset($_GET['type']) && $_GET['type'] === 'faculty')
                        echo 'selected'; ?>>Faculty</option>
                </select>
                <?php if (isset($_GET['type']) && in_array($_GET['type'], ['admin', 'faculty'])): ?>
                    <input type="hidden" name="addRoleSelect" value="<?php echo htmlspecialchars($_GET['type']); ?>"
                        id="hiddenAddRoleSelect">
                <?php else: ?>
                    <input type="hidden" name="addRoleSelect" id="hiddenAddRoleSelect" value="">
                <?php endif; ?>
            </div>
            <div id="departmentDiv" class="hidden">
                <label for="addDepartment" class="block mb-1 text-gray-700 font-semibold">Department</label>
                <select name="addDepartment" id="addDepartment"
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
                <label for="addProgram" class="block mb-1 text-gray-700 font-semibold">Program</label>
                <select name="addProgram" id="addProgram"
                    class="w-full border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300">
                    <option value="" disabled selected>Select Program</option>
                    <?php foreach ($programs as $program): ?>
                        <option value="<?= htmlspecialchars($program['ProgramID']) ?>"
                            data-department="<?= htmlspecialchars($program['DepartmentID'] ?? '') ?>">
                            <?= htmlspecialchars($program['ProgramName']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="preferredSubjectsDiv" class="hidden mt-4">
                <label for="addPreferredSubjects" class="block mb-1 text-gray-700 font-semibold">Preferred
                    Subjects</label>
                <select name="addPreferredSubjects[]" id="addPreferredSubjects" multiple size="6"
                    class="w-full border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300">
                    <!-- Options will be populated dynamically -->
                </select>
            </div>
            <div>
                <label class="block mb-1 text-gray-700 font-semibold" for="addProfilePic">Profile Picture
                    (Optional)</label>
                <input type="file" name="addProfilePic" id="addProfilePic" accept="image/*"
                    class="w-full border border-gray-300 p-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300">
            </div>
            <div class="flex gap-4">
                <button type="submit"
                    class="w-1/2 bg-lapis-lazuli text-white p-3 rounded-lg hover:bg-blue-800 transition duration-300 font-semibold shadow-md">Register</button>
                <button type="button" data-modal-hide="registerModal"
                    class="w-1/2 bg-gray-100 p-3 rounded-lg hover:bg-gray-200 transition duration-300">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRegisterModal() {
        const modal = document.getElementById('registerModal');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100', 'pointer-events-auto');
    }

    function closeRegisterModal() {
        const modal = document.getElementById('registerModal');
        modal.classList.remove('opacity-100', 'pointer-events-auto');
        modal.classList.add('opacity-0', 'pointer-events-none');
    }

    function updateRoleDependentFields() {
        const roleSelect = document.getElementById('addRoleSelect');
        const departmentDiv = document.getElementById('departmentDiv');
        const programDiv = document.getElementById('programDiv');
        const preferredSubjectsDiv = document.getElementById('preferredSubjectsDiv');
        if (roleSelect.value === 'faculty') {
            departmentDiv.classList.remove('hidden');
            programDiv.classList.remove('hidden');
            preferredSubjectsDiv.classList.remove('hidden');
        } else {
            departmentDiv.classList.add('hidden');
            programDiv.classList.add('hidden');
            preferredSubjectsDiv.classList.add('hidden');
        }
    }

    document.getElementById('addRoleSelect').addEventListener('change', function () {
        updateRoleDependentFields();
        // Update hidden input value when role select changes
        const hiddenRoleInput = document.getElementById('hiddenAddRoleSelect');
        if (hiddenRoleInput) {
            hiddenRoleInput.value = this.value;
        }
    });

    // Call on page load to set initial state based on pre-selected role
    updateRoleDependentFields();

    // Initialize hidden input value on page load
    const roleSelect = document.getElementById('addRoleSelect');
    const hiddenRoleInput = document.getElementById('hiddenAddRoleSelect');
    if (hiddenRoleInput && roleSelect) {
        hiddenRoleInput.value = roleSelect.value;
    }

    // Auto-select program to same value as department if exists
    document.getElementById('addDepartment').addEventListener('change', function () {
        const selectedDeptId = this.value;
        const programSelect = document.getElementById('addProgram');
        let found = false;
        for (let option of programSelect.options) {
            // Compare department ID as string to avoid type mismatch
            if (option.getAttribute('data-department') === selectedDeptId) {
                programSelect.value = option.value;
                found = true;
                break;
            }
        }
        if (!found) {
            programSelect.value = '';
        }
        // Trigger change event to load preferred subjects
        programSelect.dispatchEvent(new Event('change'));
    });

    // Fetch and populate preferred subjects based on selected program
    document.getElementById('addProgram').addEventListener('change', function () {
        const preferredSubjectsSelect = document.getElementById('addPreferredSubjects');
        preferredSubjectsSelect.innerHTML = '';

        const programId = this.value;
        console.log(programId);

        if (!programId) return;

        fetch('/src/partials/modals/admin/add_new_users_modal.php?action=getCurriculumSubjects&programId=' + programId)
            .then(response => response.json())
            .then(data => {
                console.log('Curriculum subjects data:', data);
                data.forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject.CurriculumID;
                    option.textContent = subject.SubjectName;
                    preferredSubjectsSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error fetching curriculum subjects:', error);
            });

        // Update department dropdown based on selected program
        const programSelect = document.getElementById('addProgram');
        const selectedOption = programSelect.options[programSelect.selectedIndex];
        const departmentId = selectedOption.getAttribute('data-department');
        const departmentSelect = document.getElementById('addDepartment');
        if (departmentId) {
            departmentSelect.value = departmentId;
        }
    });

    window.addEventListener('DOMContentLoaded', (event) => {
        const registerModal = document.getElementById('registerModal');
        const successMessage = <?= json_encode($successMessage) ?>;
        const errorMessage = <?= json_encode($errorMessage) ?>;
        if (successMessage || errorMessage) {
            openRegisterModal();
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