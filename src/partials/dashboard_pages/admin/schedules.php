<?php

require_once __DIR__ . '/functions/func_schedules.php';

$sections = getSections();
$faculties = getFaculties();

// Prepare filter display text for print header
// Find faculty full name by ID
$facultyName = '';
if (!empty($facultyFilter)) {
    foreach ($faculties as $faculty) {
        if ($faculty['FacultyID'] == $facultyFilter) {
            $facultyName = htmlspecialchars($faculty['FirstName'] . ' ' . $faculty['LastName']);
            break;
        }
    }
}

// Find section name by ID
$sectionName = '';
if (!empty($sectionFilter)) {
    foreach ($sections as $section) {
        if ($section['SectionID'] == $sectionFilter) {
            $sectionName = htmlspecialchars($section['SectionName']);
            break;
        }
    }
}

$filterTexts = [];
if (!empty($facultyName)) {
    $filterTexts[] = 'Faculty: ' . $facultyName;
}
if (!empty($dayFilter)) {
    $filterTexts[] = 'Day: ' . htmlspecialchars($dayFilter);
}
if (!empty($sectionName)) {
    $filterTexts[] = 'Section: ' . $sectionName;
}
if (!empty($search)) {
    $filterTexts[] = 'Search: ' . htmlspecialchars($search);
}

$activeSchoolYearSemesterID = getActiveSchoolYearSemesterID();

$whereClauses = [];
$queryParams = [];

if (!empty($facultyFilter)) {
    $whereClauses[] = "s.FacultyID = ?";
    $queryParams[] = $facultyFilter;
}

if (!empty($dayFilter)) {
    $whereClauses[] = "s.Day = ?";
    $queryParams[] = $dayFilter;
}

if (!empty($sectionFilter)) {
    $whereClauses[] = "s.SectionID = ?";
    $queryParams[] = $sectionFilter;
}

if ($activeSchoolYearSemesterID !== null) {
    $whereClauses[] = "s.SchoolYearSemesterID = ?";
    $queryParams[] = $activeSchoolYearSemesterID;
}

if (!empty($search)) {
    $searchKeywords = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
    if (!empty($searchKeywords)) {
        $keywordClauses = [];
        foreach ($searchKeywords as $keyword) {
            $keyword = strtolower($keyword);
            $keywordClauses[] = "LOWER(c.SubjectName) LIKE LOWER(?)";
            $keywordClauses[] = "LOWER(CONCAT(u.FirstName, ' ', u.LastName)) LIKE LOWER(?)";
            $keywordClauses[] = "LOWER(s.Day) LIKE LOWER(?)";
            $keywordClauses[] = "LOWER(r.RoomName) LIKE LOWER(?)";
            $keywordClauses[] = "LOWER(sec.SectionName) LIKE LOWER(?)";
            $queryParams[] = '%' . $keyword . '%';
            $queryParams[] = '%' . $keyword . '%';
            $queryParams[] = '%' . $keyword . '%';
            $queryParams[] = '%' . $keyword . '%';
            $queryParams[] = '%' . $keyword . '%';
        }
        $whereClauses[] = '(' . implode(' OR ', $keywordClauses) . ')';
    }
}

$whereString = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get total count for pagination
    $countSql = "SELECT COUNT(*) FROM schedules s
                 JOIN curriculums c ON s.CurriculumID = c.CurriculumID
                 JOIN facultymembers fm ON s.FacultyID = fm.FacultyID
                 JOIN users u ON fm.UserID = u.UserID
                 JOIN rooms r ON s.RoomID = r.RoomID
                 JOIN sections sec ON s.SectionID = sec.SectionID
                 $whereString";
    $stmtCount = $conn->prepare($countSql);
    $stmtCount->execute($queryParams);
    $totalRows = $stmtCount->fetchColumn();
    $totalPages = ceil($totalRows / $rowsPerPage);

    if ($totalRows > 0) {
        $sql = "SELECT s.ScheduleID, c.SubjectName, CONCAT(u.FirstName, ' ', u.LastName) AS FacultyName, s.Day, s.StartTime, s.EndTime, r.RoomName, sec.SectionName, s.RoomID, s.SectionID
                FROM schedules s
                JOIN curriculums c ON s.CurriculumID = c.CurriculumID
                JOIN facultymembers fm ON s.FacultyID = fm.FacultyID
                JOIN users u ON fm.UserID = u.UserID
                JOIN rooms r ON s.RoomID = r.RoomID
                JOIN sections sec ON s.SectionID = sec.SectionID
                $whereString
ORDER BY sec.SectionName ASC, FIELD(s.Day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), s.StartTime ASC
                LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $paramIndex = 1;
        foreach ($queryParams as $param) {
            $stmt->bindValue($paramIndex++, $param);
        }
        $stmt->bindValue($paramIndex++, $rowsPerPage, PDO::PARAM_INT);
        $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $data = [];
    }
} catch (PDOException $e) {
    $data = [];
    $totalRows = 0;
    $totalPages = 0;
}

?>
<style>
    /* Print styles */
    @media print {

        /* Hide sidebar, header, and footer */
        #sidebar,
        header,
        footer,
        #addNewBtn,
        #printBtn,
        form#filterForm,
        .inline-flex[role="group"] {
            display: none !important;
        }

        /* Hide the existing h1 title */
        h1.text-lg {
            display: none !important;
        }

        /* Show print header */
        .print-header {
            display: block !important;
            margin-bottom: 1rem;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        body {
            background: transparent !important;
        }

        /* Make the schedules section take full width with margin and white background */
        section {
            box-shadow: none !important;
            border-radius: 0 !important;
            background: transparent !important;
            padding: 0.5rem !important;
            margin: 0.5rem !important;
        }

        /* Table adjustments for print */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 10pt !important;
            margin-top: 0.5rem;
            background: transparent !important;
            margin: auto !important;
        }

        th,
        td {
            border: 1px solid #000 !important;
            padding: 4px 6px !important;
            color: #000 !important;
            text-align: left !important;
        }

        th {
            text-align: center !important;
        }

        /* Remove hover and focus styles */
        tr:hover,
        tr:focus {
            background: none !important;
        }

        /* Hide action buttons column */
        th:last-child,
        td:last-child {
            display: none !important;
        }

        th:first-child,
        td:first-child {
            text-align: center !important;
            display: none !important;
        }

        #rowCountTools {
            display: none !important;
        }

        #exportCSVBtn {
            display: none !important;
        }
    }

    /* Hide print header by default */
    .print-header {
        display: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const printBtn = document.getElementById('printBtn');
        if (printBtn) {
            printBtn.addEventListener('click', function() {
                window.print();
            });
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const exportCSVBtn = document.getElementById('exportCSVBtn');
        if (exportCSVBtn) {
            exportCSVBtn.addEventListener('click', function() {
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
                const blob = new Blob([csvContent], {
                    type: 'text/csv;charset=utf-8;'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                // Add current date to filename in year-month-day format
                const date = new Date();
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                a.download = `schedules_export_${year}-${month}-${day}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            });
        }
    });
</script>

<div class="print-header">
    <h2 style="margin-bottom: 0.5rem; font-weight: bold; font-size: 18pt;">Schedules Report</h2>
    <?php if (!empty($filterTexts)): ?>
        <div style="font-size: 12pt;">
            <?php echo implode(' | ', $filterTexts); ?>
        </div>
    <?php else: ?>
        <div style="font-size: 12pt;">All Schedules</div>
    <?php endif; ?>
</div>

<div id="messageContainer"></div>
<section class="p-4 sm:p-6 bg-white rounded shadow-md overflow-x-auto">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
        <h1 class="text-lg sm:text-xl font-semibold mb-4 md:mb-0">Schedules View</h1>
        <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0">
            <button type="button" id="addNewBtn"
                class="flex items-center justify-center px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full sm:w-auto">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14M19 12H5"></path>
                </svg>
                Add New
            </button>
            <button type="button" id="printBtn"
                class="flex items-center justify-center px-3 sm:px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 w-full sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                </svg>
                Print
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

    <!-- Filter Tools -->
    <form id="filterForm" method="get" class="mb-4">
        <input type="hidden" name="view" value="schedules" />
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0">
            <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0 items-center">
                <label for="filter-faculty" class="text-sm font-medium mr-2">Faculty:</label>
                <select id="filter-faculty" name="faculty" onchange="this.form.submit()"
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                    <option value="">All</option>
                    <?php foreach ($faculties as $faculty): ?>
                        <option value="<?php echo htmlspecialchars($faculty['FacultyID']); ?>" <?php if ($faculty['FacultyID'] == $facultyFilter)
                                                                                                    echo 'selected'; ?>>
                            <?php echo htmlspecialchars($faculty['FirstName'] . ' ' . $faculty['LastName']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="filter-day" class="text-sm font-medium ml-4">Day:</label>
                <select id="filter-day" name="day" onchange="this.form.submit()"
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                    <option value="">All</option>
                    <?php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    foreach ($days as $dayOption):
                    ?>
                        <option value="<?php echo $dayOption; ?>" <?php if ($dayOption == $dayFilter)
                                                                        echo 'selected'; ?>>
                            <?php echo $dayOption; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="filter-section" class="text-sm font-medium ml-4">Section:</label>
                <select id="filter-section" name="section" onchange="this.form.submit()"
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                    <option value="">All</option>
                    <?php foreach ($sections as $section): ?>
                        <option value="<?php echo htmlspecialchars($section['SectionID']); ?>" <?php if ($section['SectionID'] == $sectionFilter)
                                                                                                    echo 'selected'; ?>>
                            <?php echo htmlspecialchars($section['SectionName']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search subject name..."
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto ml-4" />
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
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject
                        Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Faculty
                        Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start
                        Time</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time
                    </th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room Name
                    </th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section
                        Name</th>
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
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['FacultyName']); ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['Day']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['StartTime']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['EndTime']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['RoomName']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($data[$i]['SectionName']); ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-center space-x-2">
                                <button class="text-blue-600 hover:text-blue-900 edit-btn" title="Edit"
                                    data-schedule="<?php echo htmlspecialchars($data[$i]['ScheduleID']); ?>"
                                    data-room-id="<?php echo htmlspecialchars($data[$i]['RoomID']); ?>"
                                    data-section-id="<?php echo htmlspecialchars($data[$i]['SectionID']); ?>" aria-label="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        title="Edit" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V9.456c0-1.081.768-2.015 1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                    </svg>
                                </button>
                                <button class="text-red-600 hover:text-red-900 delete-btn" title="Delete"
                                    data-schedule="<?php echo htmlspecialchars($data[$i]['ScheduleID']); ?>"
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
                        <td colspan="9" class="px-4 py-2 text-center text-gray-500">No schedules found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="rowCountTools"
        class="flex flex-col md:flex-row md:items-center md:justify-between mt-4 space-y-2 md:space-y-0">
        <div class="text-sm text-gray-700 flex items-center space-x-2">
            <span>
                Showing <?php echo ($offset + 1); ?> to <?php echo min($offset + $rowsPerPage, $totalRows); ?> of
                <?php echo $totalRows; ?> results
            </span>
            <form method="GET" action="" id="rowsPerPageForm" class="inline-block">
                <input type="hidden" name="faculty" value="<?php echo htmlspecialchars($facultyFilter); ?>">
                <input type="hidden" name="day" value="<?php echo htmlspecialchars($dayFilter); ?>">
                <input type="hidden" name="section" value="<?php echo htmlspecialchars($sectionFilter); ?>">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <input type="hidden" name="view" value="schedules">
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
                <input type="hidden" name="view" value="schedules">
                <input type="hidden" name="faculty" value="<?php echo htmlspecialchars($facultyFilter); ?>">
                <input type="hidden" name="day" value="<?php echo htmlspecialchars($dayFilter); ?>">
                <input type="hidden" name="section" value="<?php echo htmlspecialchars($sectionFilter); ?>">
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

<?php include __DIR__ . '/../../modals/admin/add_new_schedule_modal.php'; ?>
<?php include __DIR__ . '/../../modals/admin/edit_schedule_modal.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addModal = document.getElementById('addNewScheduleModal');
        const addNewBtn = document.getElementById('addNewBtn');
        const addCloseButtons = addModal ? addModal.querySelectorAll('[data-modal-hide]') : [];

        const editModal = document.getElementById('editScheduleModal');
        const editCloseButtons = editModal ? editModal.querySelectorAll('[data-modal-hide]') : [];

        function openModal(modal) {
            if (modal) {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.classList.add('opacity-100', 'pointer-events-auto');
                modal.setAttribute('aria-hidden', 'false');
                // Set focus to the first focusable element inside the modal
                const focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                if (focusable) focusable.focus();
            }
        }

        function closeModal(modal) {
            if (modal) {
                modal.classList.remove('opacity-100', 'pointer-events-auto');
                modal.classList.add('opacity-0', 'pointer-events-none');
                modal.setAttribute('aria-hidden', 'true');
                // Return focus to the add button after closing
                const addNewBtn = document.getElementById('addNewBtn');
                if (addNewBtn) {
                    addNewBtn.focus();
                }
            }
        }

        if (addNewBtn) {
            addNewBtn.addEventListener('click', function() {
                openModal(addModal);
            });
        }

        addCloseButtons.forEach(button => {
            button.addEventListener('click', function() {
                closeModal(addModal);
            });
        });

        editCloseButtons.forEach(button => {
            button.addEventListener('click', function() {
                closeModal(editModal);
            });
        });

        if (addModal) {
            addModal.addEventListener('click', function(event) {
                if (event.target === addModal) {
                    closeModal(addModal);
                }
            });
        }

        if (editModal) {
            editModal.addEventListener('click', function(event) {
                if (event.target === editModal) {
                    closeModal(editModal);
                }
            });
        }

        // Edit button click handler
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const scheduleID = this.getAttribute('data-schedule');
                const row = this.closest('tr');
                const subjectName = row.querySelector('td:nth-child(2)').textContent.trim();
                const facultyName = row.querySelector('td:nth-child(3)').textContent.trim();
                const day = row.querySelector('td:nth-child(4)').textContent.trim();
                const startTime = row.querySelector('td:nth-child(5)').textContent.trim();
                const endTime = row.querySelector('td:nth-child(6)').textContent.trim();
                const roomName = row.querySelector('td:nth-child(7)').textContent.trim();
                const sectionName = row.querySelector('td:nth-child(8)').textContent.trim();

                document.getElementById('editScheduleID').value = scheduleID;
                document.getElementById('editSubjectName').value = subjectName;
                document.getElementById('editFacultyName').value = facultyName;
                document.getElementById('editDay').value = day;
                document.getElementById('editStartTime').value = startTime;
                document.getElementById('editEndTime').value = endTime;

                const roomID = this.getAttribute('data-room-id');
                const sectionID = this.getAttribute('data-section-id');
                const editRoomSelect = document.getElementById('editRoomID');
                const editSectionSelect = document.getElementById('editSectionID');

                // Set the select values only if the option exists
                if (editRoomSelect.querySelector('option[value="' + roomID + '"]')) {
                    editRoomSelect.value = roomID;
                } else {
                    editRoomSelect.value = '';
                }

                if (editSectionSelect.querySelector('option[value="' + sectionID + '"]')) {
                    editSectionSelect.value = sectionID;
                } else {
                    editSectionSelect.value = '';
                }

                openModal(editModal);
            });
        });

        // Delete button click handler
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const scheduleID = this.getAttribute('data-schedule');
                if (confirm('Are you sure you want to delete this schedule?')) {
                    fetch('src/partials/dashboard_pages/admin/functions/func_schedules.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams({
                                'context': 'deleteSchedule',
                                'deleteScheduleID': scheduleID
                            })
                        })
                        .then(response => response.text())
                        .then(data => {
                            document.getElementById('messageContainer').innerHTML = data;
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        })
                        .catch(error => {
                            document.getElementById('messageContainer').innerHTML = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">Error deleting schedule.</div>';
                            console.error('Error:', error);
                        });
                }
            });
        });

        // Auto-dismiss messages after 3 seconds
        function autoDismissMessage() {
            setTimeout(() => {
                const messageContainer = document.getElementById('messageContainer');
                if (messageContainer) {
                    messageContainer.innerHTML = '';
                }
            }, 3000);
        }

        autoDismissMessage();
    });
</script>