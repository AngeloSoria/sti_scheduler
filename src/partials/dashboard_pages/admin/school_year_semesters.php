<?php
require_once(__DIR__ . "/functions/func_school_year_semester.php");
?>

<div id="messageContainer"></div>
<section class="p-4 sm:p-6 bg-white rounded shadow-md overflow-x-auto">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
        <h1 class="text-lg sm:text-xl font-semibold mb-4 md:mb-0">School Term</h1>
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-2 sm:space-y-0">
            <div>
                <button type="button" id="addNewBtn"
                    class="flex items-center justify-center px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 5v14M19 12H5"></path>
                    </svg>
                    Add New
                </button>
            </div>
            <div>
                <form method="GET" action="" class="flex">
                    <input type="hidden" name="view" value="school_year_semesters">
                    <input type="hidden" name="page" value="<?php echo htmlspecialchars($currentPage); ?>">
                    <input type="hidden" name="rowsPerPage" value="<?php echo htmlspecialchars($rowsPerPage); ?>">
                    <input type="text" id="searchInput" name="search" placeholder="Search school year semesters..."
                        class="border border-gray-300 rounded-l px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        value="<?php echo htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
                    <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-r hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School
                        Year</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Active
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($schoolYearSemesters)): ?>
                    <?php foreach ($schoolYearSemesters as $index => $sys): ?>
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <?php echo ($currentPage - 1) * $rowsPerPage + $index + 1; ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($sys['SchoolYear']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($sys['Semester']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap text-center"><?php echo $sys['IsActive'] ? 'Yes' : 'No'; ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-center space-x-2">
                                <button class="text-blue-600 hover:text-blue-900 edit-btn" title="Edit"
                                    data-id="<?php echo $sys['ID']; ?>"
                                    data-schoolyear="<?php echo htmlspecialchars($sys['SchoolYear']); ?>"
                                    data-semester="<?php echo htmlspecialchars($sys['Semester']); ?>"
                                    data-isactive="<?php echo htmlspecialchars($sys['IsActive']); ?>" aria-label="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        title="Edit" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                                <button class="text-red-600 hover:text-red-900 delete-btn" title="Delete"
                                    data-id="<?php echo $sys['ID']; ?>" aria-label="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        title="Delete" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-center text-gray-500">No records found.</td>
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
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <input type="hidden" name="view" value="school_year_semesters">
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
                <input type="hidden" name="view" value="school_year_semesters">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
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

<!-- Add New School Year Semester Modal -->
<?php include_once __DIR__ . '../../../modals/admin/add_new_school_year_semester_modal.php'; ?>

<!-- Edit School Year Semester Modal -->
<?php include_once __DIR__ . '../../../modals/admin/edit_school_year_semester_modal.php'; ?>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addModal = document.getElementById('addNewSchoolYearSemesterModal');
        const addNewBtn = document.getElementById('addNewBtn');
        const addCloseButtons = addModal ? addModal.querySelectorAll('[data-modal-hide]') : [];

        const editModal = document.getElementById('editSchoolYearSemesterModal');
        const editCloseButtons = editModal ? editModal.querySelectorAll('[data-modal-hide]') : [];

        function openModal(modal) {
            if (modal) {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.classList.add('opacity-100', 'pointer-events-auto');
            }
        }

        function closeModal(modal) {
            if (modal) {
                modal.classList.remove('opacity-100', 'pointer-events-auto');
                modal.classList.add('opacity-0', 'pointer-events-none');
            }
        }

        if (addNewBtn) {
            addNewBtn.addEventListener('click', function () {
                openModal(addModal);
            });
        }

        addCloseButtons.forEach(button => {
            button.addEventListener('click', function () {
                closeModal(addModal);
            });
        });

        editCloseButtons.forEach(button => {
            button.addEventListener('click', function () {
                closeModal(editModal);
            });
        });

        // Optional: close modal when clicking outside the modal content
        if (addModal) {
            addModal.addEventListener('click', function (event) {
                if (event.target === addModal) {
                    closeModal(addModal);
                }
            });
        }

        if (editModal) {
            editModal.addEventListener('click', function (event) {
                if (event.target === editModal) {
                    closeModal(editModal);
                }
            });
        }

        // Edit button click handler
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const schoolYear = this.getAttribute('data-schoolyear');
                const semester = this.getAttribute('data-semester');
                const isActive = this.getAttribute('data-isactive');

                const [startYear, endYear] = schoolYear.split('-');

                document.getElementById('editID').value = id;
                document.getElementById('editStartYear').value = startYear;
                document.getElementById('editEndYear').value = endYear;
                document.getElementById('editSemester').value = semester;
                document.getElementById('editIsActive').checked = isActive == 1;

                openModal(editModal);
            });
        });

        const messageContainer = document.getElementById('messageContainer');

        // Delete button click handler
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this record?')) {
                    fetch('src/partials/dashboard_pages/admin/functions/func_school_year_semester.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            'deleteID': id
                        })
                    })
                        .then(response => response.text())
                        .then(data => {
                            messageContainer.innerHTML = data;
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        })
                        .catch(error => {
                            messageContainer.innerHTML = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">Error deleting record.</div>';
                            console.error('Error:', error);
                        });
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

        // Call autoDismissMessage if alert message exists on page load
        const alertMessage = document.querySelector('div[role="alert"]');
        if (alertMessage) {
            setTimeout(() => {
                alertMessage.remove();
            }, 3000);
        }
    });

    // AJAX form submission for Add School Year Semester
    const addForm = document.getElementById('addSchoolYearSemesterForm');
    if (addForm) {
        addForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(addForm);
            formData.append('btnAdd', '');

            fetch(addForm.action, {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    const messageContainer = document.getElementById('messageContainer');
                    messageContainer.innerHTML = data;
                    const addModal = document.getElementById('addNewSchoolYearSemesterModal');
                    if (addModal) {
                        addModal.classList.remove('opacity-100', 'pointer-events-auto');
                        addModal.classList.add('opacity-0', 'pointer-events-none');
                    }
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    }

    // AJAX form submission for Edit School Year Semester
    const editForm = document.getElementById('editSchoolYearSemesterForm');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(editForm);
            formData.append('btnEdit', '');

            fetch(editForm.action, {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    const messageContainer = document.getElementById('messageContainer');
                    messageContainer.innerHTML = data;
                    const editModal = document.getElementById('editSchoolYearSemesterModal');
                    if (editModal) {
                        editModal.classList.remove('opacity-100', 'pointer-events-auto');
                        editModal.classList.add('opacity-0', 'pointer-events-none');
                    }
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    }
</script>