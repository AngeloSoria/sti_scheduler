<?php

if (!class_exists('Database')) {
    require_once __DIR__ . '../../../../config/dbConnection.php';
}

require_once __DIR__ . '/functions/func_curriculums.php';

?>

<div id="messageContainer"></div>
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
                <label for="filter-semester" class="text-sm font-medium ml-4">Semester:</label>
                <select id="filter-semester" name="semester" onchange="this.form.submit()"
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                    <option value="">All</option>
                    <option value="1" <?php if (isset($_GET['semester']) && $_GET['semester'] == '1')
                        echo 'selected'; ?>>
                        1</option>
                    <option value="2" <?php if (isset($_GET['semester']) && $_GET['semester'] == '2')
                        echo 'selected'; ?>>
                        2</option>
                </select>
                <label for="filter-units" class="text-sm font-medium ml-4">Units:</label>
                <select id="filter-units" name="units" onchange="this.form.submit()"
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                    <option value="">All</option>
                    <?php for ($u = 1; $u <= 6; $u++): ?>
                        <option value="<?php echo $u; ?>" <?php if (isset($_GET['units']) && $_GET['units'] == $u)
                               echo 'selected'; ?>><?php echo $u; ?></option>
                    <?php endfor; ?>
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
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject
                    </th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year
                        Level</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester
                    </th>
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
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['SubjectName']); ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['Units']); ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['YearLevel']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['Semester']); ?></td>
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
                <?php
                $maxPagesToShow = 5;
                if ($totalPages <= $maxPagesToShow) {
                    $startPage = 1;
                    $endPage = $totalPages;
                } else {
                    $half = floor($maxPagesToShow / 2);
                    if ($currentPage - $half <= 0) {
                        $startPage = 1;
                        $endPage = $maxPagesToShow;
                    } elseif ($currentPage + $half > $totalPages) {
                        $startPage = $totalPages - $maxPagesToShow + 1;
                        $endPage = $totalPages;
                    } else {
                        $startPage = $currentPage - $half;
                        $endPage = $currentPage + $half;
                    }
                }
                for ($i = $startPage; $i <= $endPage; $i++): ?>
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
    <?php include __DIR__ . '/../../modals/admin/add_new_curriculum_modal.php'; ?>

    <!-- Edit Curriculum Modal -->
    <div id="editCurriculumModal" tabindex="-1" aria-hidden="true"
        class="opacity-0 pointer-events-none overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full transition-opacity duration-300 ease-in-out">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300 ease-in-out"></div>
        <div class="relative p-4 w-full max-w-4xl max-h-full z-10">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Edit Curriculum
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                        data-modal-hide="editCurriculumModal">
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
                    <form method="POST" action="" id="editCurriculumForm" class="grid grid-cols-3 gap-4">
                        <input type="hidden" name="editCurriculumID" id="editCurriculumID" />
                        <div>
                            <label for="editCourseID" class="block text-gray-700 text-sm font-bold mb-2">
                                Course ID:
                            </label>
                            <input type="number" name="editCourseID" id="editCourseID"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline no-spinner"
                                placeholder="e.g., 1628">
                            <p class="text-gray-500 text-xs italic">Enter the course ID if applicable.</p>
                        </div>
                        <div>
                            <label for="editSubjectArea" class="block text-gray-700 text-sm font-bold mb-2">
                                Subject Area: <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="editSubjectArea" id="editSubjectArea" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="GEDC" pattern="^[a-zA-Z]{2,}$"
                                title="Subject area must be at least 2 letters and contain only letters.">
                            <p class="text-gray-500 text-xs italic">Enter the subject area (letters only, no spaces or
                                numbers).</p>
                        </div>
                        <div>
                            <label for="editCatalogNo" class="block text-gray-700 text-sm font-bold mb-2">
                                Catalog Number:
                            </label>
                            <input type="number" name="editCatalogNo" id="editCatalogNo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline no-spinner"
                                placeholder="e.g., 1005">
                            <p class="text-gray-500 text-xs italic">Enter the catalog number.</p>
                        </div>
                        <div class="col-span-2">
                            <label for="editSubjectName" class="block text-gray-700 text-sm font-bold mb-2">
                                Subject Name: <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="editSubjectName" id="editSubjectName" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="e.g., Mathematics in the Modern World"
                                pattern="^(?=.*[a-zA-Z])[a-zA-Z\s]{2,}$"
                                title="Subject name must be at least 2 letters and contain only letters and spaces.">
                            <p class="text-gray-500 text-xs italic">Enter the full name of the subject.</p>
                        </div>
                        <div>
                            <label for="editUnits" class="block text-gray-700 text-sm font-bold mb-2">
                                Units:
                            </label>
                            <input type="number" name="editUnits" id="editUnits"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline no-spinner"
                                placeholder="e.g., 3">
                            <p class="text-gray-500 text-xs italic">Enter the number of units.</p>
                        </div>
                        <div>
                            <label for="editProgramName" class="block text-gray-700 text-sm font-bold mb-2">
                                Program: <span class="text-red-500">*</span>
                            </label>
                            <select name="editProgramName" id="editProgramName" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="" disabled selected>Select a Program</option>
                                <?php foreach ($programs as $program): ?>
                                    <option value="<?php echo htmlspecialchars($program); ?>">
                                        <?php echo htmlspecialchars($program); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-gray-500 text-xs italic">Choose the program this subject belongs to.</p>
                        </div>
                        <div>
                            <label for="editYearLevel" class="block text-gray-700 text-sm font-bold mb-2">
                                Year Level: <span class="text-red-500">*</span>
                            </label>
                            <select name="editYearLevel" id="editYearLevel" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="" disabled selected>Select Year Level</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                            <p class="text-gray-500 text-xs italic">Indicate the year level when this subject is
                                offered.</p>
                        </div>
                        <div>
                            <label for="editSemester" class="block text-gray-700 text-sm font-bold mb-2">
                                Semester:
                            </label>
                            <select name="editSemester" id="editSemester"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="" disabled selected>Select Semester</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </select>
                            <p class="text-gray-500 text-xs italic">Select the semester when this subject is offered.
                            </p>
                        </div>
                        <div class="col-span-3 flex justify-end">
                            <button type="button"
                                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                                data-modal-hide="editCurriculumModal">
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
        document.getElementById('editCurriculumForm').addEventListener('submit', function (event) {
            const subjectNameInput = document.getElementById('editSubjectName');
            const subjectAreaInput = document.getElementById('editSubjectArea');
            const subjectNameValue = subjectNameInput.value.trim();
            const subjectAreaValue = subjectAreaInput.value.trim();
            const subjectNameRegex = /^[a-zA-Z\s]{2,}$/;
            const subjectAreaRegex = /^[a-zA-Z]{2,}$/;
            if (!subjectNameRegex.test(subjectNameValue)) {
                event.preventDefault();
                alert('Subject name must be at least 2 letters and contain only letters and spaces.');
                subjectNameInput.focus();
                return;
            }
            if (!subjectAreaRegex.test(subjectAreaValue)) {
                event.preventDefault();
                alert('Subject area must be at least 2 letters and contain only letters with no spaces or numbers.');
                subjectAreaInput.focus();
                return;
            }
        });
    </script>
    <script>
        +        // Removed duplicate event listener for editCurriculumForm submit
    </script>

    <script>
            document.addEventListener('DOMContentLoaded', function () {
                const addModal = document.getElementById('addNewCurriculumModal');
                const addNewBtn = document.getElementById('addNewBtn');
                const addCloseButtons = addModal ? addModal.querySelectorAll('[data-modal-hide]') : [];

                const editModal = document.getElementById('editCurriculumModal');
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
                        const curriculumID = this.getAttribute('data-subject');
                        // Fetch curriculum data from the row or via AJAX if needed
                        const row = this.closest('tr');
                        const subjectName = row.querySelector('td:nth-child(1)').textContent.trim();
                        const units = row.querySelector('td:nth-child(2)').textContent.trim();
                        const yearLevel = row.querySelector('td:nth-child(3)').textContent.trim();
                        const semester = row.querySelector('td:nth-child(4)').textContent.trim();
                        const programName = row.querySelector('td:nth-child(5)').textContent.trim();

                        // Fetch full curriculum data via AJAX
                        fetch(`src/partials/dashboard_pages/admin/functions/get_curriculum.php?curriculumID=${curriculumID}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    alert(data.error);
                                    return;
                                }
                                document.getElementById('editCurriculumID').value = data.CurriculumID || '';
                                document.getElementById('editSubjectName').value = data.SubjectName || '';
                                document.getElementById('editUnits').value = data.Units || '';
                                document.getElementById('editCourseID').value = data.CourseID || '';
                                document.getElementById('editSubjectArea').value = data.SubjectArea || '';
                                document.getElementById('editCatalogNo').value = data.CatalogNo || '';
                                document.getElementById('editYearLevel').value = data.YearLevel || '';
                                document.getElementById('editSemester').value = data.Semester || '';
                                document.getElementById('editProgramName').value = data.ProgramName || '';

                                openModal(editModal);
                            })
                            .catch(error => {
                                alert('Error fetching curriculum data.');
                                console.error('Error:', error);
                            });
                    });
                });

                const messageContainer = document.getElementById('messageContainer');

                // Delete button click handler
                document.querySelectorAll('.delete-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const curriculumID = this.getAttribute('data-subject');
                        if (confirm('Are you sure you want to delete this curriculum?')) {
                            // Send AJAX request to delete the curriculum
                            fetch('src/partials/dashboard_pages/admin/functions/func_curriculums.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: new URLSearchParams({
                                    'deleteCurriculumID': curriculumID
                                })
                            })
                                .then(response => response.text())
                                .then(data => {
                                    console.log('Delete response:', data);
                                    messageContainer.innerHTML = data;
                                    // Optionally, reload the page after a delay to show updated data
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1500);
                                })
                                .catch(error => {
                                    messageContainer.innerHTML = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">Error deleting curriculum.</div>';
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
                    }, 3000); // 3 seconds
                }
            });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const exportCSVBtn = document.getElementById('exportCSVBtn');
            exportCSVBtn.addEventListener('click', function () {
                const table = document.querySelector('table.min-w-full');
                if (!table) return;

                let csvContent = '';
                const rows = table.querySelectorAll('thead tr, tbody tr');

                rows.forEach(row => {
                    // Only include visible rows
                    if (row.offsetParent === null) return;

                    const cols = row.querySelectorAll('th, td');
                    let rowData = [];
                    // Exclude the last column (Actions)
                    const colsToProcess = Array.from(cols).slice(0, -1);
                    colsToProcess.forEach(col => {
                        // Only include visible columns
                        if (col.offsetParent === null) return;
                        // Escape double quotes by doubling them
                        let cellText = col.textContent.trim().replace(/"/g, '""');
                        // Wrap cell text in double quotes
                        rowData.push(`"${cellText}"`);
                    });
                    csvContent += rowData.join(',') + '\r\n';
                });

                // Create a Blob with CSV data and trigger download
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                // Add current date to filename in year-month-day format
                const date = new Date();
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                a.download = `curriculums_export_${year}-${month}-${day}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            });
        });
    </script>