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

$successMessage = isset($_SESSION['successMessage']) ? $_SESSION['successMessage'] : null;
$errorMessage = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : null;
unset($_SESSION['successMessage'], $_SESSION['errorMessage']);

$departments = getDepartments();
$programs = getPrograms();

?>

<div id="registerModal" tabindex="-1" aria-hidden="true"
    class="opacity-0 pointer-events-none overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full transition-opacity duration-300 ease-in-out">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300 ease-in-out"></div>
    <div class="relative p-6 w-full max-w-2xl max-h-full z-10">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b rounded-t">
                <h3 class="text-2xl font-semibold text-gray-900">
                    Register New User
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
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
            <div class="p-6 space-y-6">
                <?php if ($successMessage): ?>
                    <div class="mb-4 p-3 bg-green-200 text-green-800 rounded"><?= htmlspecialchars($successMessage) ?></div>
                <?php elseif ($errorMessage): ?>
                    <div class="mb-4 p-3 bg-red-200 text-red-800 rounded"><?= htmlspecialchars($errorMessage) ?></div>
                <?php endif; ?>
                <form method="POST" action="src/partials/dashboard_pages/admin/functions/func_users.php" id="addUserForm"
                    enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="addUser">
                    <input type="hidden" name="type" value="<?php echo htmlspecialchars($_GET['type'] ?? ''); ?>">
                    <div class="flex gap-4">
                        <div class="w-1/2">
                            <label for="addUsername" class="block text-gray-700 text-sm font-bold mb-2">
                                Username: <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="addUsername" id="addUsername" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Username">
                        </div>
                        <div class="w-1/2">
                            <label for="addPassword" class="block text-gray-700 text-sm font-bold mb-2">
                                Password: <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="addPassword" id="addPassword" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Password">
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-1/2">
                            <label for="addConfirmPassword" class="block text-gray-700 text-sm font-bold mb-2">
                                Confirm Password: <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="addConfirmPassword" id="addConfirmPassword" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Confirm Password">
                            <span id="confirmPasswordError" class="text-red-500 text-sm hidden">Passwords do not match.</span>
                        </div>
                        <div class="w-1/2"></div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-1/3">
                            <label for="addFirstName" class="block text-gray-700 text-sm font-bold mb-2">
                                First Name: <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="addFirstName" id="addFirstName" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="First Name">
                        </div>
                        <div class="w-1/3">
                            <label for="addMiddleName" class="block text-gray-700 text-sm font-bold mb-2">
                                Middle Name:
                            </label>
                            <input type="text" name="addMiddleName" id="addMiddleName"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Middle Name (Optional)">
                        </div>
                        <div class="w-1/3">
                            <label for="addLastName" class="block text-gray-700 text-sm font-bold mb-2">
                                Last Name: <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="addLastName" id="addLastName" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Last Name">
                        </div>
                    </div>
                    <div>
                        <label for="addRoleSelect" class="block text-gray-700 text-sm font-bold mb-2">
                            Role: <span class="text-red-500">*</span>
                        </label>
                        <select id="addRoleSelect" name="addRoleSelect"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required <?php if (isset($_GET['type']) && in_array($_GET['type'], ['admin', 'faculty'])) echo 'disabled'; ?>>
                            <option value="admin" <?php if (isset($_GET['type']) && $_GET['type'] === 'admin') echo 'selected'; ?>>Admin</option>
                            <option value="faculty" <?php if (isset($_GET['type']) && $_GET['type'] === 'faculty') echo 'selected'; ?>>Faculty</option>
                        </select>
                        <?php if (isset($_GET['type']) && in_array($_GET['type'], ['admin', 'faculty'])): ?>
                            <input type="hidden" name="addRoleSelect" value="<?php echo htmlspecialchars($_GET['type']); ?>" id="hiddenAddRoleSelect">
                        <?php else: ?>
                            <input type="hidden" name="addRoleSelect" id="hiddenAddRoleSelect" value="">
                        <?php endif; ?>
                    </div>
                    <div id="departmentDiv" class="hidden">
                        <label for="addDepartment" class="block text-gray-700 text-sm font-bold mb-2">
                            Department:
                        </label>
                        <select name="addDepartment" id="addDepartment"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled selected>Select Department</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= htmlspecialchars($department['DepartmentID']) ?>">
                                    <?= htmlspecialchars($department['DepartmentName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="programDiv" class="hidden">
                        <label for="addProgram" class="block text-gray-700 text-sm font-bold mb-2">
                            Program:
                        </label>
                        <select name="addProgram" id="addProgram"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled selected>Select Program</option>
                            <?php foreach ($programs as $program): ?>
                                <option value="<?= htmlspecialchars($program['ProgramID']) ?>"
                                    data-department="<?= htmlspecialchars($program['DepartmentID'] ?? '') ?>">
                                    <?= htmlspecialchars($program['ProgramName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="preferredSubjectsDiv" class="hidden">
                        <label for="addPreferredSubjects" class="block text-gray-700 text-sm font-bold mb-2">
                            Preferred Subjects:
                        </label>
                        <select name="addPreferredSubjects[]" id="addPreferredSubjects" multiple size="6"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <!-- Options will be populated dynamically -->
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="addProfilePic">
                            Profile Picture (Optional):
                        </label>
                        <input type="file" name="addProfilePic" id="addProfilePic" accept="image/*"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex justify-end">
                        <button type="button"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                            onclick="closeRegisterModal()">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-3">
                            Register
                        </button>
                    </div>
                </form>
            </div>
        </div>
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

    // Confirm password validation
    document.getElementById('addUserForm').addEventListener('submit', function (e) {
        const password = document.getElementById('addPassword').value;
        const confirmPassword = document.getElementById('addConfirmPassword').value;
        const errorSpan = document.getElementById('confirmPasswordError');
        if (password !== confirmPassword) {
            errorSpan.classList.remove('hidden');
            e.preventDefault();
        } else {
            errorSpan.classList.add('hidden');
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