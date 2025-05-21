<?php

require_once __DIR__ . '/../../../../config/dbConnection.php';

if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit();
}

$userID = $_SESSION['user']['id'];

$db = new Database();
$conn = $db->getConnection();

// Get FacultyID from UserID
$stmtFaculty = $conn->prepare("SELECT FacultyID FROM facultymembers WHERE UserID = ?");
$stmtFaculty->execute([$userID]);
$faculty = $stmtFaculty->fetch(PDO::FETCH_ASSOC);

if (!$faculty) {
    echo "Faculty record not found.";
    exit();
}

$facultyID = $faculty['FacultyID'];

// Filters
$dayFilter = isset($_GET['day']) ? $_GET['day'] : '';
$sectionFilter = isset($_GET['section']) ? $_GET['section'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Pagination
$rowsPerPageOptions = [5, 10, 20, 50, 100];
$rowsPerPage = isset($_GET['rowsPerPage']) && in_array($_GET['rowsPerPage'], $rowsPerPageOptions) ? $_GET['rowsPerPage'] : 10;
$currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($currentPage - 1) * $rowsPerPage;

// Build where clauses
$whereClauses = ["s.FacultyID = ?"];
$queryParams = [$facultyID];

if (!empty($dayFilter)) {
    $whereClauses[] = "s.Day = ?";
    $queryParams[] = $dayFilter;
}

if (!empty($sectionFilter)) {
    $whereClauses[] = "s.SectionID = ?";
    $queryParams[] = $sectionFilter;
}

if (!empty($search)) {
    $searchKeywords = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
    if (!empty($searchKeywords)) {
        $keywordClauses = [];
        foreach ($searchKeywords as $keyword) {
            $keywordClause = [];
            $keyword = strtolower($keyword);
            $keywordClause[] = "LOWER(c.SubjectName) LIKE LOWER(?)";
            $keywordClause[] = "LOWER(r.RoomName) LIKE LOWER(?)";
            $keywordClause[] = "LOWER(se.SectionName) LIKE LOWER(?)";
            $keywordClauses[] = '(' . implode(' OR ', $keywordClause) . ')';
            $likeKeyword = '%' . $keyword . '%';
            $queryParams[] = $likeKeyword;
            $queryParams[] = $likeKeyword;
            $queryParams[] = $likeKeyword;
        }
        $whereClauses[] = '(' . implode(' AND ', $keywordClauses) . ')';
    }
}

$whereString = "WHERE " . implode(" AND ", $whereClauses);

// Count total rows
$countSql = "SELECT COUNT(*) FROM schedules s
    JOIN curriculums c ON s.CurriculumID = c.CurriculumID
    JOIN rooms r ON s.RoomID = r.RoomID
    JOIN sections se ON s.SectionID = se.SectionID
    $whereString";

$stmtCount = $conn->prepare($countSql);
$stmtCount->execute($queryParams);
$totalRows = $stmtCount->fetchColumn();
$totalPages = ceil($totalRows / $rowsPerPage);

// Fetch data
$sql = "SELECT s.ScheduleID, c.SubjectName, s.Day, s.StartTime, s.EndTime, r.RoomName, se.SectionName
    FROM schedules s
    JOIN curriculums c ON s.CurriculumID = c.CurriculumID
    JOIN rooms r ON s.RoomID = r.RoomID
    JOIN sections se ON s.SectionID = se.SectionID
    $whereString
    ORDER BY s.Day, s.StartTime
    LIMIT ?, ?";

$stmt = $conn->prepare($sql);

try {
    $paramIndex = 1;
    foreach ($queryParams as $param) {
        $stmt->bindValue($paramIndex++, $param);
    }
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $rowsPerPage, PDO::PARAM_INT);

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $data = [];
    $totalRows = 0;
    $totalPages = 0;
}

// Fetch sections for filter dropdown (only sections with schedules for this faculty)
$stmtSections = $conn->prepare("
    SELECT DISTINCT se.SectionID, se.SectionName
    FROM schedules s
    JOIN sections se ON s.SectionID = se.SectionID
    WHERE s.FacultyID = ?
    ORDER BY se.SectionName
");
$stmtSections->execute([$facultyID]);
$sections = $stmtSections->fetchAll(PDO::FETCH_ASSOC);

// Days array for filter dropdown
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

// Generate color codes for each subject
$subjectColors = [];
$colorPalette = [
    '#60a5fa', // blue-400
    '#f59e42', // orange-400
    '#34d399', // green-400
    '#f87171', // red-400
    '#a78bfa', // purple-400
    '#fbbf24', // yellow-400
    '#38bdf8', // sky-400
    '#f472b6', // pink-400
    '#4ade80', // emerald-400
    '#facc15', // amber-400
    '#818cf8', // indigo-400
    '#fb7185', // rose-400
];
$colorIndex = 0;
foreach ($data as $row) {
    $subject = $row['SubjectName'];
    if (!isset($subjectColors[$subject])) {
        $subjectColors[$subject] = $colorPalette[$colorIndex % count($colorPalette)];
        $colorIndex++;
    }
}

?>

<section class="p-2 sm:p-3 w-full h-full">
    <section class="p-2 sm:p-6 bg-white rounded shadow-md w-full">

        <div class="flex flex-col sm:flex-row items-center justify-between mb-4 w-full">

            <h1 class="text-lg font-semibold mb-2 sm:mb-0">My Schedule</h1>

            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <button id="toggleViewBtn" type="button"
                    class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                    View as Calendar
                </button>
                <button id="printBtn" type="button"
                    class="px-4 py-2 rounded bg-green-600 text-white flex justify-center items-center gap-2 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 w-full sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                    </svg>
                    Print Schedule
                </button>
            </div>
        </div>

        <form id="filterForm" method="get"
            class="mb-4 flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-2 sm:space-y-0 w-full">
            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-2">
                <label for="filter-day" class="text-sm font-medium">Day:</label>
                <select id="filter-day" name="day" onchange="this.form.submit()"
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[120px]">
                    <option value="">All</option>
                    <?php foreach ($days as $dayOption): ?>
                        <option value="<?php echo $dayOption; ?>" <?php if ($dayOption == $dayFilter)
                               echo 'selected'; ?>>
                            <?php echo $dayOption; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-2">
                <label for="filter-section" class="text-sm font-medium">Section:</label>
                <select id="filter-section" name="section" onchange="this.form.submit()"
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[120px]">
                    <option value="">All</option>
                    <?php foreach ($sections as $section): ?>
                        <option value="<?php echo htmlspecialchars($section['SectionID']); ?>" <?php if ($section['SectionID'] == $sectionFilter)
                               echo 'selected'; ?>>
                            <?php echo htmlspecialchars($section['SectionName']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-2">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search..."
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[120px]" />
                <button type="submit"
                    class="mt-2 sm:mt-0 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Search
                </button>
            </div>
        </form>


        <!-- Table view (wrap with a div for toggling) -->
        <div id="tableView">
            <!-- Print header (hidden by default, shown only in print) -->
            <div class="print-header mx-auto">
                <h2 style="margin-bottom: 0.5rem; font-weight: bold; font-size: 18pt;">My Schedule</h2>
                <?php
                $filterTexts = [];
                if (!empty($dayFilter))
                    $filterTexts[] = 'Day: ' . htmlspecialchars($dayFilter);
                if (!empty($sectionFilter)) {
                    foreach ($sections as $section) {
                        if ($section['SectionID'] == $sectionFilter) {
                            $filterTexts[] = 'Section: ' . htmlspecialchars($section['SectionName']);
                            break;
                        }
                    }
                }
                if (!empty($search))
                    $filterTexts[] = 'Search: ' . htmlspecialchars($search);
                ?>
                <?php if (!empty($filterTexts)): ?>
                    <div style="font-size: 12pt;">
                        <?php echo implode(' | ', $filterTexts); ?>
                    </div>
                <?php else: ?>
                    <div style="font-size: 12pt;">All Schedules</div>
                <?php endif; ?>
            </div>

            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subject
                                Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Day
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Start
                                Time</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                End
                                Time
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Room
                                Name
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Section
                                Name</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($data)): ?>
                            <?php foreach ($data as $index => $row): ?>
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap"><?php echo $offset + $index + 1; ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <span
                                            style="background:<?php echo $subjectColors[$row['SubjectName']]; ?>;padding:2px 8px;border-radius:4px;">
                                            <?php echo htmlspecialchars($row['SubjectName']); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($row['Day']); ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <?php echo date('g:i A', strtotime($row['StartTime'])); ?>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <?php echo date('g:i A', strtotime($row['EndTime'])); ?>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($row['RoomName']); ?>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($row['SectionName']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-4 py-2 text-center text-gray-500">No records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Card view for mobile -->
            <div class="sm:hidden space-y-4">
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $index => $row): ?>
                        <div class="border border-gray-300 rounded p-4 shadow-sm">
                            <div class="font-semibold mb-2">
                                <span
                                    style="background:<?php echo $subjectColors[$row['SubjectName']]; ?>;color:#fff;padding:2px 8px;border-radius:4px;">
                                    <?php echo htmlspecialchars($row['SubjectName']); ?>
                                </span>
                            </div>
                            <div><strong>Day:</strong> <?php echo htmlspecialchars($row['Day']); ?></div>
                            <div><strong>Start Time:</strong> <?php echo date('g:i A', strtotime($row['StartTime'])); ?></div>
                            <div><strong>End Time:</strong> <?php echo date('g:i A', strtotime($row['EndTime'])); ?></div>
                            <div><strong>Room:</strong> <?php echo htmlspecialchars($row['RoomName']); ?></div>
                            <div><strong>Section:</strong> <?php echo htmlspecialchars($row['SectionName']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-gray-500">No records found.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Calendar view (hidden by default) -->
        <div id="calendarView" class="hidden">
            <!-- Print header (hidden by default, shown only in print) -->
            <div class="print-header mx-auto">
                <h2 style="margin-bottom: 0.5rem; font-weight: bold; font-size: 18pt;">My Schedule</h2>
                <?php
                $filterTexts = [];
                if (!empty($dayFilter))
                    $filterTexts[] = 'Day: ' . htmlspecialchars($dayFilter);
                if (!empty($sectionFilter)) {
                    foreach ($sections as $section) {
                        if ($section['SectionID'] == $sectionFilter) {
                            $filterTexts[] = 'Section: ' . htmlspecialchars($section['SectionName']);
                            break;
                        }
                    }
                }
                if (!empty($search))
                    $filterTexts[] = 'Search: ' . htmlspecialchars($search);
                ?>
                <?php if (!empty($filterTexts)): ?>
                    <div style="font-size: 12pt;">
                        <?php echo implode(' | ', $filterTexts); ?>
                    </div>
                <?php else: ?>
                    <div style="font-size: 12pt;">All Schedules</div>
                <?php endif; ?>
            </div>
            <!-- Desktop/tablet calendar table -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-2 py-2 text-xs font-medium text-gray-500 uppercase w-28 min-w-24 border-r border-gray-300 text-center">
                                Time</th>
                            <?php foreach ($days as $day): ?>
                                <th
                                    class="px-2 py-2 text-xs font-medium text-gray-500 uppercase w-40 min-w-32 border-r border-gray-300 text-center">
                                    <?php echo $day; ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        // Collect all unique time slots
                        $timeSlots = [];
                        foreach ($data as $row) {
                            $slot = $row['StartTime'] . ' - ' . $row['EndTime'];
                            if (!in_array($slot, $timeSlots)) {
                                $timeSlots[] = $slot;
                            }
                        }
                        sort($timeSlots);

                        // Group data by [time][day]
                        $calendarData = [];
                        foreach ($data as $row) {
                            $slot = $row['StartTime'] . ' - ' . $row['EndTime'];
                            $calendarData[$slot][$row['Day']][] = $row;
                        }

                        // Render rows by time slot
                        foreach ($timeSlots as $slot):
                            // Convert slot to 12-hour format
                            list($start, $end) = explode(' - ', $slot);
                            $start12 = date('g:i A', strtotime($start));
                            $end12 = date('g:i A', strtotime($end));
                            $slot12 = $start12 . ' - ' . $end12;
                            ?>
                            <tr>
                                <td
                                    class="px-2 py-2 font-semibold text-gray-700 w-28 min-w-24 border-r border-gray-300 text-center">
                                    <?php echo $slot12; ?>
                                </td>
                                <?php foreach ($days as $day): ?>
                                    <td class="px-2 py-2 align-top w-40 min-w-32 border-r border-gray-300">
                                        <?php
                                        if (!empty($calendarData[$slot][$day])) {
                                            foreach ($calendarData[$slot][$day] as $sched) {
                                                $color = $subjectColors[$sched['SubjectName']];
                                                echo '<div id="cal-item" class="mb-2 p-2 rounded" style="background:' . $color . ';color:#fff;">';
                                                echo '<div class="font-semibold">' . htmlspecialchars($sched['SubjectName']) . '</div>';
                                                echo '<div class="text-xs">' . htmlspecialchars($sched['SectionName']) . ' | ' . htmlspecialchars('R' . $sched['RoomName']) . '</div>';
                                                echo '</div>';
                                            }
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Mobile card view -->
            <div class="sm:hidden space-y-4">
                <?php
                // Group data by day and time for mobile
                foreach ($days as $day):
                    $hasSched = false;
                    foreach ($data as $row) {
                        if ($row['Day'] === $day) {
                            $hasSched = true;
                            break;
                        }
                    }
                    if ($hasSched):
                        ?>
                        <div class="mb-4">
                            <div class="font-bold text-blue-700 mb-2"><?php echo $day; ?></div>
                            <?php
                            foreach ($timeSlots as $slot):
                                if (!empty($calendarData[$slot][$day])):
                                    // Convert slot to 12-hour format
                                    list($start, $end) = explode(' - ', $slot);
                                    $start12 = date('g:i A', strtotime($start));
                                    $end12 = date('g:i A', strtotime($end));
                                    $slot12 = $start12 . ' - ' . $end12;
                                    foreach ($calendarData[$slot][$day] as $sched):
                                        $color = $subjectColors[$sched['SubjectName']];
                                        ?>
                                        <div id="cal-item" class="mb-2 p-3 rounded shadow"
                                            style="background:<?php echo $color; ?>;color:#fff;">
                                            <div class="font-semibold"><?php echo htmlspecialchars($sched['SubjectName']); ?></div>
                                            <div class="text-xs"><?php echo htmlspecialchars($sched['SectionName']); ?> |
                                                <?php echo htmlspecialchars('R' . $sched['RoomName']); ?>
                                            </div>
                                            <div class="text-xs mt-1"><span class="font-bold">Time:</span> <?php echo $slot12; ?></div>
                                        </div>
                                        <?php
                                    endforeach;
                                endif;
                            endforeach;
                            ?>
                        </div>
                        <?php
                    endif;
                endforeach;
                if (empty($data)):
                    ?>
                    <div class="text-center text-gray-500">No records found.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pagination -->
        <div id="paginationTool"
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4 space-y-2 sm:space-y-0 w-full">
            <div class="text-sm text-gray-700">
                Showing <?php echo ($offset + 1); ?> to <?php echo min($offset + $rowsPerPage, $totalRows); ?> of
                <?php echo $totalRows; ?> results
            </div>
            <div class="inline-flex rounded-md shadow-sm w-full sm:w-auto" role="group" aria-label="Pagination">
                <form method="GET" id="paginationForm" class="flex flex-wrap gap-1 w-full sm:w-auto">
                    <input type="hidden" name="day" value="<?php echo htmlspecialchars($dayFilter); ?>">
                    <input type="hidden" name="section" value="<?php echo htmlspecialchars($sectionFilter); ?>">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="rowsPerPage" value="<?php echo $rowsPerPage; ?>">
                    <button type="submit" name="page" value="<?php echo max(1, $currentPage - 1); ?>"
                        class="px-3 py-1 border border-gray-300 bg-white text-gray-500 hover:bg-gray-100 rounded-l-md w-full sm:w-auto">Previous</button>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <button type="submit" name="page" value="<?php echo $i; ?>"
                            class="px-3 py-1 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-100 <?php echo ($i == $currentPage) ? 'font-bold' : ''; ?> w-full sm:w-auto"><?php echo $i; ?></button>
                    <?php endfor; ?>
                    <button type="submit" name="page" value="<?php echo min($totalPages, $currentPage + 1); ?>"
                        class="px-3 py-1 border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 rounded-r-md w-full sm:w-auto">Next</button>
                </form>
            </div>
        </div>
    </section>
</section>


<style>
    @media print {
        body * {
            visibility: hidden !important;
        }

        #tableView,
        #tableView *,
        #calendarView,
        #calendarView * {
            visibility: visible !important;
            margin: auto !important;
        }

        .print-header {
            display: block !important;
            visibility: visible !important;
            margin-bottom: 1rem;
            font-family: Arial, sans-serif;
            text-align: center;
            color: #000;
        }

        #tableView,
        #calendarView {
            position: relative !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            margin: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
        }

        /* Hide card view, filter form, toggle/print buttons, pagination */
        #filterForm,
        #toggleViewBtn,
        #printBtn,
        #paginationTool,
        .sm\:hidden,
        .flex.flex-col.sm\:flex-row.sm\:items-center.sm\:justify-between.mt-4,
        .flex.flex-col.sm\:flex-row.sm\:items-center.sm\:space-x-4,
        .flex.gap-2,
        h1.text-lg {
            display: none !important;
        }

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

        tr:hover,
        tr:focus {
            background: none !important;
        }

        #cal-item {
            color: #000 !important;
            text-align: center !important;
        }
    }

    .print-header {
        display: none;
    }

    @media screen {

        /* Optional: Make sure table layout is fixed for consistent column widths */
        #calendarView table {
            table-layout: fixed;
        }

        #calendarView th,
        #calendarView td {
            border-right: 1px solid #d1d5db;
            /* Tailwind gray-300 */
        }

        #calendarView th:last-child,
        #calendarView td:last-child {
            border-right: none;
        }
    }
</style>

<script>
    // Toggle view logic
    const toggleBtn = document.getElementById('toggleViewBtn');
    const tableView = document.getElementById('tableView');
    const calendarView = document.getElementById('calendarView');
    const filterForm = document.getElementById('filterForm');
    const printBtn = document.getElementById('printBtn');

    // Remember view in localStorage
    function setView(view) {
        if (view === 'calendar') {
            tableView.classList.add('hidden');
            calendarView.classList.remove('hidden');
            toggleBtn.textContent = 'View as Table';
            filterForm.classList.add('hidden');
        } else {
            tableView.classList.remove('hidden');
            calendarView.classList.add('hidden');
            toggleBtn.textContent = 'View as Calendar';
            filterForm.classList.remove('hidden');
        }
        localStorage.setItem('scheduleView', view);
    }

    toggleBtn.addEventListener('click', function () {
        const current = tableView.classList.contains('hidden') ? 'calendar' : 'table';
        setView(current === 'table' ? 'calendar' : 'table');
    });

    // On load, restore view
    document.addEventListener('DOMContentLoaded', function () {
        const saved = localStorage.getItem('scheduleView') || 'table';
        setView(saved);
    });

    // Print button logic
    if (printBtn) {
        printBtn.addEventListener('click', function () {
            window.print();
        });
    }
</script>