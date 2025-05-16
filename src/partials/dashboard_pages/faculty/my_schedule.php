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

// Fetch sections for filter dropdown
$stmtSections = $conn->prepare("SELECT SectionID, SectionName FROM sections ORDER BY SectionName");
$stmtSections->execute();
$sections = $stmtSections->fetchAll(PDO::FETCH_ASSOC);

// Days array for filter dropdown
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

?>

<section class="p-4 sm:p-6 bg-white rounded shadow-md">
    <h1 class="text-lg font-semibold mb-4">My schedule</h1>

    <form id="filterForm" method="get"
        class="mb-4 flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-2 sm:space-y-0">
        <label for="filter-day" class="text-sm font-medium">Day:</label>
        <select id="filter-day" name="day" onchange="this.form.submit()"
            class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
            <option value="">All</option>
            <?php foreach ($days as $dayOption): ?>
                <option value="<?php echo $dayOption; ?>" <?php if ($dayOption == $dayFilter)
                       echo 'selected'; ?>>
                    <?php echo $dayOption; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="filter-section" class="text-sm font-medium">Section:</label>
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

        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search..."
            class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto" />
        <button type="submit"
            class="ml-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Search
        </button>
    </form>

    <div class="hidden sm:block overflow-x-auto">
        <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject
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
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $index => $row): ?>
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo $offset + $index + 1; ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($row['SubjectName']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($row['Day']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($row['StartTime']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($row['EndTime']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($row['RoomName']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($row['SectionName']); ?></td>
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
                    <div class="font-semibold mb-2"><?php echo htmlspecialchars($row['SubjectName']); ?></div>
                    <div><strong>Day:</strong> <?php echo htmlspecialchars($row['Day']); ?></div>
                    <div><strong>Start Time:</strong> <?php echo htmlspecialchars($row['StartTime']); ?></div>
                    <div><strong>End Time:</strong> <?php echo htmlspecialchars($row['EndTime']); ?></div>
                    <div><strong>Room:</strong> <?php echo htmlspecialchars($row['RoomName']); ?></div>
                    <div><strong>Section:</strong> <?php echo htmlspecialchars($row['SectionName']); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center text-gray-500">No records found.</div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4 space-y-2 sm:space-y-0">
        <div class="text-sm text-gray-700">
            Showing <?php echo ($offset + 1); ?> to <?php echo min($offset + $rowsPerPage, $totalRows); ?> of
            <?php echo $totalRows; ?> results
        </div>
        <div class="inline-flex rounded-md shadow-sm" role="group" aria-label="Pagination">
            <form method="GET" id="paginationForm" class="flex flex-wrap gap-1">
                <input type="hidden" name="day" value="<?php echo htmlspecialchars($dayFilter); ?>">
                <input type="hidden" name="section" value="<?php echo htmlspecialchars($sectionFilter); ?>">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <input type="hidden" name="rowsPerPage" value="<?php echo $rowsPerPage; ?>">
                <button type="submit" name="page" value="<?php echo max(1, $currentPage - 1); ?>"
                    class="px-3 py-1 border border-gray-300 bg-white text-gray-500 hover:bg-gray-100 rounded-l-md">Previous</button>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <button type="submit" name="page" value="<?php echo $i; ?>"
                        class="px-3 py-1 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-100 <?php echo ($i == $currentPage) ? 'font-bold' : ''; ?>"><?php echo $i; ?></button>
                <?php endfor; ?>
                <button type="submit" name="page" value="<?php echo min($totalPages, $currentPage + 1); ?>"
                    class="px-3 py-1 border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 rounded-r-md">Next</button>
            </form>
        </div>
    </div>
</section>