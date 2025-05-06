<?php

if (!class_exists('Database')) {
    require_once __DIR__ . '/functions/func_users.php';
}

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

<!-- Edit User Modal -->
<div id="editUserModal" tabindex="-1" aria-hidden="true"
    class="opacity-0 pointer-events-none overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full transition-opacity duration-300 ease-in-out">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300 ease-in-out"></div>
    <div class="relative p-4 w-full max-w-md max-h-full z-10">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b rounded-t">
                <h3 class="text-xl font-semibold text-gray-900">
                    Edit User
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                    data-modal-hide="editUserModal">
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
                <form method="POST" action="src/partials/dashboard_pages/admin/functions/func_users.php"
                    id="editUserForm" enctype="multipart/form-data">
                    <input type="hidden" name="editUserID" id="editUserID" />
                    <div class="mb-4">
                        <label for="editFirstName" class="block text-gray-700 text-sm font-bold mb-2">
                            First Name:
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="editFirstName" id="editFirstName" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="First Name">
                    </div>
                    <div class="mb-4">
                        <?php if ($type === 'admin' || $type === 'faculty'): ?>
                            <label for="editMiddleName" class="block text-gray-700 text-sm font-bold mb-2">
                                Middle Name:
                            </label>
                            <input type="text" name="editMiddleName" id="editMiddleName"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Middle Name">
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="editLastName" class="block text-gray-700 text-sm font-bold mb-2">
                            Last Name:
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="editLastName" id="editLastName" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="Last Name">
                    </div>
                    <?php if ($type === 'admin' || $type === 'faculty'): ?>
                        <div class="mb-4">
                            <label for="editUsernameDisplay" class="block text-gray-700 text-sm font-bold mb-2">
                                Username:
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="editUsernameDisplay" readonly
                                class="bg-gray-100 cursor-not-allowed shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Username">
                            <input type="hidden" name="editUsername" id="editUsername" />
                        </div>
                    <?php endif; ?>
                    <div class="mb-4">
                        <label for="editPassword" class="block text-gray-700 text-sm font-bold mb-2">
                            Password:
                        </label>
                        <input type="password" name="editPassword" id="editPassword"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="New Password (leave blank to keep current)">
                    </div>
                    <div class="mb-4">
                        <label for="editProfilePic" class="block text-gray-700 text-sm font-bold mb-2">
                            Profile Picture:
                        </label>
                        <input type="file" name="editProfilePic" id="editProfilePic" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                            file:rounded file:border-0
                            file:text-sm file:font-semibold
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100">
                    </div>
                    <div class="flex justify-end">
                        <button type="button"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                            data-modal-hide="editUserModal">
                            Cancel
                        </button>
                        <button type="submit" name="btnEdit"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-3">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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

        // Edit button click handler
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
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

                document.getElementById('editUserID').value = userID;
                document.getElementById('editFirstName').value = firstName;
                <?php if ($type === 'admin' || $type === 'faculty'): ?>
                    document.getElementById('editMiddleName').value = middleName;
                    document.getElementById('editUsername').value = username; // set hidden input to original username
                    document.getElementById('editUsernameDisplay').value = maskUsername(username); // set visible input to masked username
                <?php endif; ?>
                document.getElementById('editLastName').value = lastName;
                document.getElementById('editPassword').value = '';

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

                            // Find the row with matching userID
                            const rows = document.querySelectorAll('tbody tr');
                            rows.forEach(row => {
                                const editBtn = row.querySelector('.edit-btn');
                                if (editBtn && editBtn.getAttribute('data-user') === userID) {
                                    row.querySelector('td:nth-child(2)').textContent = firstName;
                                    row.querySelector('td:nth-child(3)').textContent = lastName;
                                    row.querySelector('td:nth-child(4)').textContent = username;
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