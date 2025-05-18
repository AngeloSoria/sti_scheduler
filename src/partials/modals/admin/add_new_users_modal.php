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
    <!-- Modal width changed to max-w-4xl -->
    <div class="relative p-4 w-full max-w-4xl max-h-full z-10">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b rounded-t">
                <!-- Title changed to "Register" and font size to text-xl -->
                <h3 class="text-xl font-semibold text-gray-900">
                    Register
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
            <form method="POST" action="src/partials/dashboard_pages/admin/functions/func_users.php" id="addUserForm"
                enctype="multipart/form-data" class="grid grid-cols-3 gap-4 p-6 auto-rows-auto">
                <input type="hidden" name="action" value="addUser">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($_GET['type'] ?? ''); ?>">
                <div class="col-span-3 grid grid-cols-3 gap-4">
                    <!-- Username, Password, Confirm Password -->
                    <div>
                        <label for="addUsername" class="block text-gray-700 text-sm font-bold mb-2">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="addUsername" id="addUsername" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="Username">
                    </div>
                    <div>
                        <label for="addPassword" class="block text-gray-700 text-sm font-bold mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="addPassword" id="addPassword" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="Password">
                    </div>
                    <div>
                        <label for="addConfirmPassword" class="block text-gray-700 text-sm font-bold mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="addConfirmPassword" id="addConfirmPassword" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="Confirm Password">
                        <span id="confirmPasswordError" class="text-red-500 text-sm hidden">Passwords do not
                            match.</span>
                    </div>
                </div>
                <div class="col-span-3 grid grid-cols-3 gap-4">
                    <!-- First, Middle, Last Name -->
                    <div>
                        <label for="addFirstName" class="block text-gray-700 text-sm font-bold mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="addFirstName" id="addFirstName" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="First Name">
                    </div>
                    <div>
                        <label for="addMiddleName" class="block text-gray-700 text-sm font-bold mb-2">
                            Middle Name
                        </label>
                        <input type="text" name="addMiddleName" id="addMiddleName"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="Middle Name (Optional)">
                    </div>
                    <div>
                        <label for="addLastName" class="block text-gray-700 text-sm font-bold mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="addLastName" id="addLastName" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="Last Name">
                    </div>
                </div>
                <div class="col-span-3 grid grid-cols-3 gap-4">
                    <!-- Role, Department/Program/Profile Pic -->
                    <div>
                        <label for="addRoleSelect" class="block text-gray-700 text-sm font-bold mb-2">
                            Role
                        </label>
                        <select id="addRoleSelect" name="addRoleSelect"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
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
                    <?php if (isset($_GET['type']) && $_GET['type'] === 'admin'): ?>
                        <div>
                            <label for="addProfilePic" class="block text-gray-700 text-sm font-bold mb-2">
                                Profile Picture (Optional)
                            </label>
                            <input type="file" name="addProfilePic" id="addProfilePic" accept="image/*"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div></div>
                    <?php else: ?>
                        <div id="departmentDiv" class="hidden">
                            <label for="addDepartment" class="block text-gray-700 text-sm font-bold mb-2">
                                Department
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
                                Program
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
                    <?php endif; ?>
                </div>
                <?php if (!(isset($_GET['type']) && $_GET['type'] === 'admin')): ?>
                    <div class="col-span-3 grid grid-cols-3 gap-4">
                        <div class="col-span-3" id="preferredSubjectsDiv" class="hidden">
                            <label for="addPreferredSubjects" class="block text-gray-700 text-sm font-bold mb-2">
                                Preferred Subjects
                            </label>
                            <div class="flex flex-col gap-2 items-start mb-2">
                                <!-- Semester filter radio buttons -->
                                <div id="semesterFilterGroup" class="flex gap-4 mb-2">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="semesterFilter" value="all" id="semesterFilterAll" checked
                                            class="form-radio accent-indigo-600 h-5 w-5 transition duration-150 ease-in-out focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        <span class="ml-2 text-gray-700 font-medium">All</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="semesterFilter" value="1" id="semesterFilter1"
                                            class="form-radio accent-indigo-600 h-5 w-5 transition duration-150 ease-in-out focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        <span class="ml-2 text-gray-700 font-medium">1</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="semesterFilter" value="2" id="semesterFilter2"
                                            class="form-radio accent-indigo-600 h-5 w-5 transition duration-150 ease-in-out focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        <span class="ml-2 text-gray-700 font-medium">2</span>
                                    </label>
                                </div>
                                <!-- Custom subject list with checkboxes -->
                                <div id="addPreferredSubjectsList"
                                    class="w-full max-h-48 overflow-y-auto border rounded p-2 bg-white">
                                    <!-- Options will be populated dynamically -->
                                </div>
                                <!-- Hidden input container to hold selected CurriculumIDs as array -->
                                <div id="addPreferredSubjectsHiddenContainer"></div>
                            </div>
                        </div>
                        <div class="col-span-3">
                            <label for="addProfilePic" class="block text-gray-700 text-sm font-bold mb-2">
                                Profile Picture (Optional)
                            </label>
                            <input type="file" name="addProfilePic" id="addProfilePic" accept="image/*"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>
                <?php endif; ?>
                <div class="col-span-3 flex justify-end gap-3">
                    <button type="button"
                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                        onclick="closeRegisterModal()">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Register
                    </button>
                </div>
            </form>
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

        if (!roleSelect) return;

        if (roleSelect.value === 'faculty') {
            if (departmentDiv) departmentDiv.classList.remove('hidden');
            if (programDiv) programDiv.classList.remove('hidden');
            if (preferredSubjectsDiv) preferredSubjectsDiv.classList.remove('hidden');
        } else {
            if (departmentDiv) departmentDiv.classList.add('hidden');
            if (programDiv) programDiv.classList.add('hidden');
            if (preferredSubjectsDiv) preferredSubjectsDiv.classList.add('hidden');
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

    window.addEventListener('DOMContentLoaded', function () {
        updateRoleDependentFields();
        const registerModal = document.getElementById('registerModal');
        const successMessage = <?= json_encode($successMessage) ?>;
        const errorMessage = <?= json_encode($errorMessage) ?>;
        if (successMessage || errorMessage) {
            openRegisterModal();
        }
        // Only run these if faculty fields exist
        if (document.getElementById('addDepartment')) {
            triggerSemesterFilterChange();
            updatePreferredSubjectsHiddenInput();
        }
    });

    // Faculty-only: Auto-select program to same value as department if exists
    const addDepartment = document.getElementById('addDepartment');
    if (addDepartment) {
        addDepartment.addEventListener('change', function () {
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
    }

    // Faculty-only: Helper to update hidden input with selected CurriculumIDs
    function updatePreferredSubjectsHiddenInput() {
        const container = document.getElementById('addPreferredSubjectsHiddenContainer');
        if (!container) return;
        container.innerHTML = '';
        const checked = Array.from(document.querySelectorAll('#addPreferredSubjectsList input[type="checkbox"]:checked'))
            .map(cb => cb.value);
        checked.forEach(val => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'addPreferredSubjects[]';
            input.value = val;
            container.appendChild(input);
        });
    }

    // Faculty-only: Populate preferred subjects as checkbox list
    const addProgram = document.getElementById('addProgram');
    if (addProgram) {
        addProgram.addEventListener('change', function () {
            const preferredSubjectsList = document.getElementById('addPreferredSubjectsList');
            if (!preferredSubjectsList) return;
            preferredSubjectsList.innerHTML = '';

            const programId = this.value;
            if (!programId) return;

            fetch('/src/partials/modals/admin/add_new_users_modal.php?action=getCurriculumSubjects&programId=' + programId)
                .then(response => response.json())
                .then(data => {
                    data.forEach(subject => {
                        const row = document.createElement('div');
                        row.className = 'flex items-center gap-2 py-1 px-2 rounded hover:bg-indigo-50 transition cursor-pointer group';

                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.value = subject.CurriculumID;
                        checkbox.setAttribute('data-semester', subject.Semester || '1');
                        checkbox.className = 'accent-indigo-600 h-5 w-5 rounded border-gray-300 focus:ring-indigo-500';

                        // On click, update hidden input
                        checkbox.addEventListener('change', updatePreferredSubjectsHiddenInput);

                        const label = document.createElement('span');
                        label.className = 'ml-2 text-gray-700 font-medium cursor-pointer flex-1 select-none';
                        label.textContent = subject.SubjectName;

                        // Make clicking the row or label toggle the checkbox
                        row.addEventListener('click', function (e) {
                            // Prevent double toggle if checkbox itself is clicked
                            if (e.target !== checkbox) {
                                checkbox.checked = !checkbox.checked;
                                checkbox.dispatchEvent(new Event('change'));
                            }
                        });
                        label.addEventListener('click', function (e) {
                            // Prevent bubbling to row if label is clicked
                            e.stopPropagation();
                            checkbox.checked = !checkbox.checked;
                            checkbox.dispatchEvent(new Event('change'));
                        });

                        row.appendChild(checkbox);
                        row.appendChild(label);
                        preferredSubjectsList.appendChild(row);
                    });
                    // Trigger filter update after loading options
                    triggerSemesterFilterChange();
                    updatePreferredSubjectsHiddenInput();
                })
                .catch(error => {
                    console.error('Error fetching curriculum subjects:', error);
                });

            // Update department dropdown based on selected program
            const programSelect = document.getElementById('addProgram');
            const selectedOption = programSelect.options[programSelect.selectedIndex];
            const departmentId = selectedOption.getAttribute('data-department');
            const departmentSelect = document.getElementById('addDepartment');
            if (departmentId && departmentSelect) {
                departmentSelect.value = departmentId;
            }
        });
    }

    // Faculty-only: Semester filter for preferred subjects (checkbox version)
    const semesterRadios = document.querySelectorAll('input[name="semesterFilter"]');
    if (semesterRadios.length > 0) {
        semesterRadios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                const selectedSemester = this.value;
                const checkboxes = document.querySelectorAll('#addPreferredSubjectsList input[type="checkbox"]');
                checkboxes.forEach(cb => {
                    const semester = cb.getAttribute('data-semester');
                    const row = cb.closest('div');
                    if (selectedSemester === 'all' || semester === selectedSemester) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                        cb.checked = false; // Uncheck if hidden
                    }
                });
                updatePreferredSubjectsHiddenInput();
            });
        });
    }

    // Faculty-only: Trigger filter update after loading options
    function triggerSemesterFilterChange() {
        const checkedRadio = document.querySelector('input[name="semesterFilter"]:checked');
        if (checkedRadio) {
            checkedRadio.dispatchEvent(new Event('change'));
        }
    }

    // Confirm password validation (runs for all roles)
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