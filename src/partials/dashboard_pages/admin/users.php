<?php

require_once __DIR__ . '/functions/func_users.php';

?>

<?php include_once __DIR__ . '/../../modals/admin/add_new_users_modal.php'; ?>


<section class="p-4 sm:p-6 bg-white rounded shadow-md overflow-x-auto">
    <?php
    $type = strtolower($_GET['type'] ?? '');
    if ($type === 'faculty') {
        $headerText = 'Faculty View';
    } elseif ($type === 'admin') {
        $headerText = 'Admin View';
    } else {
        $headerText = 'Users View';
    }
    ?>
    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
        <h1 class="text-lg sm:text-xl font-semibold mb-4 md:mb-0"><?php echo htmlspecialchars($headerText); ?></h1>
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-2 sm:space-y-0">
            <button id="btnAddUser" type="button"
                class="flex items-center justify-center px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                onclick="openRegisterModal()">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 5v14M19 12H5"></path>
                </svg>
                Add User
            </button>
            <form id="filterForm" method="get" class="flex">
                <input type="hidden" name="view" value="users" />
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($_GET['type'] ?? ''); ?>" />
                <input type="text" id="search" name="search"
                    value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                    placeholder="Search by first name, last name, or username"
                    class="border border-gray-300 rounded-l px-2 py-1 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                <button type="submit"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-r hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Search
                </button>
            </form>
        </div>
    </div>
    <div id="messageContainer" class="mb-4"></div>
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First
                        Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Name
                    </th>
                    <?php if ($type === 'admin' || $type === 'faculty'): ?>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username
                        </th>
                    <?php endif; ?>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($data)): ?>
                    <?php for ($i = 0; $i < count($data); $i++): ?>
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo $offset + $i + 1; ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['FirstName']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['LastName']); ?></td>
                            <?php if ($type === 'admin' || $type === 'faculty'): ?>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <?php
                                    $username = $data[$i]['Username'];
                                    if (strlen($username) > 3) {
                                        $visiblePart = substr($username, 0, 3);
                                        $maskedPart = str_repeat('*', strlen($username) - 3);
                                        echo htmlspecialchars($visiblePart . $maskedPart);
                                    } else {
                                        echo htmlspecialchars($username);
                                    }
                                    ?>
                                </td>
                            <?php endif; ?>
                            <td class="px-4 py-2 whitespace-nowrap text-center space-x-2">
                                <button class="text-blue-600 hover:text-blue-900 edit-btn" title="Edit"
                                    data-user="<?php echo htmlspecialchars($data[$i]['UserID']); ?>"
                                    data-middle-name="<?php echo htmlspecialchars($data[$i]['MiddleName']); ?>"
                                    aria-label="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        title="Edit" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                                <button class="text-red-600 hover:text-red-900 delete-btn" title="Delete"
                                    data-user="<?php echo htmlspecialchars($data[$i]['UserID']); ?>" aria-label="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        title="Delete" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    <?php endfor; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?php echo ($type === 'admin') ? 5 : 4; ?>"
                            class="px-4 py-2 text-center text-gray-500">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mt-4 space-y-2 md:space-y-0">
        <div class="text-sm text-gray-700 flex items-center space-x-2">
            <span>
                Showing <?php echo ($offset + 1); ?> to <?php echo min($offset + $rowsPerPage, $totalRows); ?> of
                <?php echo $totalRows; ?> results
            </span>
            <form method="GET" action="" id="rowsPerPageForm" class="inline-block">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($_GET['type'] ?? ''); ?>">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <input type="hidden" name="view" value="users">
                <select id="rows-per-page" name="rowsPerPage"
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onchange="document.getElementById('rowsPerPageForm').submit();">
                    <?php foreach ($rowsPerPageOptions as $option): ?>
                        <option value="<?php echo $option; ?>" <?php if ($option == $rowsPerPage)
                               echo 'selected'; ?>>
                            <?php echo $option; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <div class="inline-flex rounded-md shadow-sm" role="group" aria-label="Pagination">
            <form method="GET" id="paginationForm">
                <input type="hidden" name="view" value="users">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($_GET['type'] ?? ''); ?>">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <input type="hidden" name="rowsPerPage" value="<?php echo $rowsPerPage; ?>">
                <button type="submit" name="page" value="<?php echo max(1, $currentPage - 1); ?>"
                    class="px-3 py-1 border border-gray-300 bg-white text-gray-500 hover:bg-gray-100 rounded-l-md w-full md:w-auto mb-2 md:mb-0">Previous</button>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <button type="submit" name="page" value="<?php echo $i; ?>"
                        class="px-3 py-1 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-100 w-full md:w-auto mb-2 md:mb-0 <?php echo ($i == $currentPage) ? 'font-bold' : ''; ?>">
                        <?php echo $i; ?>
                    </button>
                <?php endfor; ?>
                <button type="submit" name="page" value="<?php echo min($totalPages, $currentPage + 1); ?>"
                    class="px-3 py-1 border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 rounded-r-md w-full md:w-auto mb-2 md:mb-0">Next</button>
            </form>
        </div>
    </div>

</section>

<!-- EDIT USER MODAL -->
<?php include_once __DIR__ . '/../../modals/admin/edit_user_modal.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const messageContainer = document.getElementById('messageContainer');

        // Delete button click handler
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                const userID = this.getAttribute('data-user');
                if (confirm('Are you sure you want to delete this user?')) {
                    fetch('src/partials/dashboard_pages/admin/functions/func_users.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            'action': 'deleteUser',
                            'deleteUserID': userID
                        })
                    })
                        .then(response => response.text())
                        .then(data => {
                            messageContainer.innerHTML = data;
                            // Reload page after 1.5 seconds to show updated data
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        })
                        .catch(error => {
                            messageContainer.innerHTML = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">Error deleting user.</div>';
                            console.error('Error:', error);
                        });
                }
            });
        });

        // Edit button click handler (role-dependent)
        document.querySelectorAll('.edit-btn').forEach(function (editBtn) {
            editBtn.addEventListener('click', function () {
                const userID = this.getAttribute('data-user');
                const middleName = this.getAttribute('data-middle-name') || '';
                const row = this.closest('tr');
                const firstName = row.querySelector('td:nth-child(2)').textContent.trim();
                const lastName = row.querySelector('td:nth-child(3)').textContent.trim();
                let username = '';
                <?php if ($type === 'admin' || $type === 'faculty'): ?>
                    username = row.querySelector('td:nth-child(4)').textContent.trim();
                <?php endif; ?>

                function maskUsername(username) {
                    if (username.length <= 3) {
                        return username;
                    }
                    const visiblePart = username.substring(0, 3);
                    const maskedPart = '*'.repeat(username.length - 3);
                    return visiblePart + maskedPart;
                }

                // Set values for common fields
                if (document.getElementById('editUserID')) document.getElementById('editUserID').value = userID;
                if (document.getElementById('editFirstName')) document.getElementById('editFirstName').value = firstName;
                <?php if ($type === 'admin' || $type === 'faculty'): ?>
                    if (document.getElementById('editMiddleName')) document.getElementById('editMiddleName').value = middleName;
                    if (document.getElementById('editUsername')) document.getElementById('editUsername').value = username;
                    if (document.getElementById('editUsernameDisplay')) document.getElementById('editUsernameDisplay').value = maskUsername(username);
                <?php endif; ?>
                if (document.getElementById('editLastName')) document.getElementById('editLastName').value = lastName;
                if (document.getElementById('editPassword')) document.getElementById('editPassword').value = '';

                // Role-dependent fields
                const role = '<?php echo $type; ?>';

                if (role === 'faculty') {
                    const editDepartmentDiv = document.getElementById('editDepartmentDiv');
                    const editProgramDiv = document.getElementById('editProgramDiv');
                    const editPreferredSubjectsDiv = document.getElementById('editPreferredSubjectsDiv');
                    const editDepartment = document.getElementById('editDepartment');
                    const editProgram = document.getElementById('editProgram');
                    const editPreferredSubjects = document.getElementById('editPreferredSubjects');

                    if (editDepartmentDiv) editDepartmentDiv.classList.remove('hidden');
                    if (editProgramDiv) editProgramDiv.classList.remove('hidden');
                    if (editPreferredSubjectsDiv) editPreferredSubjectsDiv.classList.remove('hidden');

                    // Only run faculty logic if all elements exist
                    if (editDepartment && editProgram && editPreferredSubjects) {
                        // Fetch and populate user's current department, program, and preferred subjects
                        fetch('src/partials/dashboard_pages/admin/functions/func_users.php?action=getUserDetails&userID=' + userID)
                            .then(response => response.json())
                            .then(data => {
                                if (data.departmentID) {
                                    editDepartment.value = data.departmentID;
                                }
                                if (data.programID) {
                                    editProgram.value = data.programID;
                                }
                                // Fetch subjects for the program
                                fetch('src/partials/modals/admin/add_new_users_modal.php?action=getCurriculumSubjects&programId=' + data.programID)
                                    .then(response => response.json())
                                    .then(subjects => {
                                        editPreferredSubjects.innerHTML = '';
                                        subjects.forEach(subject => {
                                            const option = document.createElement('option');
                                            option.value = subject.CurriculumID;
                                            option.textContent = subject.SubjectName;
                                            if (data.preferredSubjects && data.preferredSubjects.includes(subject.CurriculumID)) {
                                                option.selected = true;
                                            }
                                            editPreferredSubjects.appendChild(option);
                                        });
                                    });
                            });

                        // Remove previous event listeners if any (optional, for safety)
                        editDepartment.onchange = null;
                        editProgram.onchange = null;

                        // Event listeners for cascading selects
                        editDepartment.addEventListener('change', function () {
                            const selectedDeptId = this.value;
                            let found = false;
                            for (let option of editProgram.options) {
                                if (option.getAttribute('data-department') === selectedDeptId) {
                                    editProgram.value = option.value;
                                    found = true;
                                    break;
                                }
                            }
                            if (!found) {
                                editProgram.value = '';
                            }
                            editProgram.dispatchEvent(new Event('change'));
                        });

                        editProgram.addEventListener('change', function () {
                            const programId = this.value;
                            editPreferredSubjects.innerHTML = '';
                            if (!programId) return;
                            fetch('src/partials/modals/admin/add_new_users_modal.php?action=getCurriculumSubjects&programId=' + programId)
                                .then(response => response.json())
                                .then(subjects => {
                                    subjects.forEach(subject => {
                                        const option = document.createElement('option');
                                        option.value = subject.CurriculumID;
                                        option.textContent = subject.SubjectName;
                                        editPreferredSubjects.appendChild(option);
                                    });
                                });
                            // Update department select based on program
                            const selectedOption = editProgram.options[editProgram.selectedIndex];
                            const departmentId = selectedOption.getAttribute('data-department');
                            if (departmentId) {
                                editDepartment.value = departmentId;
                            }
                        });
                    }
                } else {
                    // Hide faculty-only fields for non-faculty roles
                    const editDepartmentDiv = document.getElementById('editDepartmentDiv');
                    const editProgramDiv = document.getElementById('editProgramDiv');
                    const editPreferredSubjectsDiv = document.getElementById('editPreferredSubjectsDiv');
                    if (editDepartmentDiv) editDepartmentDiv.classList.add('hidden');
                    if (editProgramDiv) editProgramDiv.classList.add('hidden');
                    if (editPreferredSubjectsDiv) editPreferredSubjectsDiv.classList.add('hidden');
                }

                const editModal = document.getElementById('editUserModal');
                if (editModal) {
                    editModal.classList.remove('opacity-0', 'pointer-events-none');
                    editModal.classList.add('opacity-100', 'pointer-events-auto');
                }
            });
        });

        // Edit form submit handler with AJAX
        const editUserForm = document.getElementById('editUserForm');
        if (editUserForm) {
            editUserForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(editUserForm);
                formData.append('btnEdit', 'Save Changes'); // Add btnEdit parameter for PHP detection
                formData.append('action', 'editUser'); // Explicitly add action for clarity
                fetch('src/partials/dashboard_pages/admin/functions/func_users.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.text())
                    .then(data => {
                        const messageContainer = document.getElementById('messageContainer');
                        messageContainer.innerHTML = data;

                        // Close modal on success
                        if (data.includes('Success!')) {
                            const editModal = document.getElementById('editUserModal');
                            if (editModal) {
                                editModal.classList.remove('opacity-100', 'pointer-events-auto');
                                editModal.classList.add('opacity-0', 'pointer-events-none');
                            }
                            // Update the table row with new data
                            const userID = document.getElementById('editUserID').value;
                            const firstName = document.getElementById('editFirstName').value;
                            const lastName = document.getElementById('editLastName').value;
                            const username = document.getElementById('editUsername').value;

                            // Mask username as in the table
                            function maskUsername(username) {
                                if (username.length <= 3) {
                                    return username;
                                }
                                const visiblePart = username.substring(0, 3);
                                const maskedPart = '*'.repeat(username.length - 3);
                                return visiblePart + maskedPart;
                            }

                            // Find the row with matching userID
                            const rows = document.querySelectorAll('tbody tr');
                            rows.forEach(row => {
                                const editBtn = row.querySelector('.edit-btn');
                                if (editBtn && editBtn.getAttribute('data-user') === userID) {
                                    row.querySelector('td:nth-child(2)').textContent = firstName;
                                    row.querySelector('td:nth-child(3)').textContent = lastName;
                                    row.querySelector('td:nth-child(4)').textContent = maskUsername(username);
                                }
                            });
                        }

                        // Auto-dismiss messages after 3 seconds
                        setTimeout(() => {
                            messageContainer.innerHTML = '';
                        }, 3000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        }

        // Modal close buttons
        document.querySelectorAll('[data-modal-hide]').forEach(button => {
            button.addEventListener('click', function () {
                const modalId = this.getAttribute('data-modal-hide');
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('opacity-100', 'pointer-events-auto');
                    modal.classList.add('opacity-0', 'pointer-events-none');
                }
            });
        });

        // Auto-dismiss messages after 3 seconds
        function autoDismissMessage() {
            setTimeout(() => {
                if (messageContainer) {
                    messageContainer.innerHTML = '';
                }
            }, 3000);
        }

        const alertMessage = document.querySelector('div[role="alert"]');
        if (alertMessage) {
            autoDismissMessage();
        }
    });
</script>