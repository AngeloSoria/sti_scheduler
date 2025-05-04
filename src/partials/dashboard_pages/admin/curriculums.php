<?php

require_once __DIR__ . '/functions/func_curriculums.php';

?>

<section class="p-4 sm:p-6 bg-white rounded shadow-md overflow-x-auto">
    <form id="importCSVForm" method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
            <h1 class="text-lg sm:text-xl font-semibold mb-4 md:mb-0">Curriculum View</h1>
            <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0">
                <button type="button" id="addNewBtn"
                    class="flex items-center justify-center px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full sm:w-auto">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 5v14M19 12H5"></path>
                    </svg>
                    Add New
                </button>
                <input type="file" name="csvFile" accept=".csv"
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto mb-2 sm:mb-0"
                    required />
                <input type="hidden" name="program" value="<?php echo htmlspecialchars($programFilter); ?>">
                <input type="hidden" name="year" value="<?php echo htmlspecialchars($yearFilter); ?>">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <input type="hidden" name="page" value="<?php echo $currentPage; ?>" />
                <button type="submit" name="btnImport"
                    class="flex items-center justify-center px-3 sm:px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                    </svg>
                    Import CSV
                </button>
                <button type="button" id="exportCSVBtn"
                    class="flex items-center justify-center px-3 sm:px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 w-full sm:w-auto">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Export CSV
                </button>
            </div>
        </div>

    </form>

    <!-- Filter Tools -->
    <form id="filterForm" method="get" class="mb-4">
        <input type="hidden" name="view" value="curriculums" />
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0">
            <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0 items-center">
            </div>
            <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0 items-center">
                <label for="filter-program" class="text-sm font-medium mr-2">Program:</label>
                <select id="filter-program" name="program" onchange="this.form.submit()"
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                    <option value="">All</option>
                    <?php foreach ($programs as $program): ?>
                        <option value="<?php echo htmlspecialchars($program); ?>" <?php if ($program == $programFilter)
                               echo 'selected'; ?>>
                            <?php echo htmlspecialchars($program); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="filter-year" class="text-sm font-medium ml-4">Year Level:</label>
                <select id="filter-year" name="year" onchange="this.form.submit()"
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                    <option value="">All</option>
                    <?php foreach ($yearLevels as $yearLevel): ?>
                        <option value="<?php echo htmlspecialchars($yearLevel); ?>" <?php if ($yearLevel == $yearFilter)
                               echo 'selected'; ?>>
                            <?php echo htmlspecialchars($yearLevel); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search..."
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto" />
                <button type="submit"
                    class="ml-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mx-auto" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#
                    </th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject
                    </th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year
                        Level</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($data)): ?>
                    <?php for ($i = 0; $i < count($data); $i++): ?>
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo $offset + $i + 1; ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['SubjectName']); ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['CreditUnit']); ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['Year']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['ProgramName']); ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-center space-x-2">
                                <button class="text-blue-600 hover:text-blue-900 edit-btn" title="Edit"
                                    data-subject="<?php echo htmlspecialchars($data[$i]['CurriculumID']); ?>" aria-label="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        title="Edit" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                                <button class="text-red-600 hover:text-red-900 delete-btn" title="Delete"
                                    data-subject="<?php echo htmlspecialchars($data[$i]['CurriculumID']); ?>"
                                    aria-label="Delete">
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
                        <td colspan="5" class="px-4 py-2 text-center text-gray-500">No curriculums found.</td>
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
                <input type="hidden" name="program" value="<?php echo htmlspecialchars($programFilter); ?>">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <input type="hidden" name="year" value="<?php echo htmlspecialchars($yearFilter); ?>">
                <input type="hidden" name="view" value="curriculums">
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
                <input type="hidden" name="view" value="curriculums">
                <input type="hidden" name="program" value="<?php echo htmlspecialchars($programFilter); ?>">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <input type="hidden" name="year" value="<?php echo htmlspecialchars($yearFilter); ?>">
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
    <?php include __DIR__ . '../../../modals/admin/add_new_curriculum_modal.php'; ?>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('addNewCurriculumModal');
        const addNewBtn = document.getElementById('addNewBtn');
        const closeButtons = modal ? modal.querySelectorAll('[data-modal-hide]') : [];

        function openModal() {
            if (modal) {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.classList.add('opacity-100', 'pointer-events-auto');
            }
        }

        function closeModal() {
            if (modal) {
                modal.classList.remove('opacity-100', 'pointer-events-auto');
                modal.classList.add('opacity-0', 'pointer-events-none');
            }
        }

        if (addNewBtn) {
            addNewBtn.addEventListener('click', function () {
                openModal();
            });
        }

        closeButtons.forEach(button => {
            button.addEventListener('click', function () {
                closeModal();
            });
        });

        // Optional: close modal when clicking outside the modal content
        if (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });
        }
    });
</script>