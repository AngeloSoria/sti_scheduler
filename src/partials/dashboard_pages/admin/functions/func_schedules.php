<?php

require_once(__DIR__ . "../../../../../../config/dbConnection.php");

$db = new Database();
$conn = $db->getConnection();

function getPreferredSubjectsByFaculty($facultyId)
{
    try {
        $db = new Database();
        $dbConn = $db->getConnection();
        $stmt = $dbConn->prepare("SELECT c.CurriculumID, c.SubjectName FROM preferredsubjects p JOIN curriculums c ON p.CurriculumID = c.CurriculumID WHERE p.FacultyID = ? ORDER BY c.SubjectName ASC");
        $stmt->execute([$facultyId]);
        return $stmt->fetchAll($dbConn::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getRooms()
{
    try {
        $db = new Database();
        $dbConn = $db->getConnection();
        $stmt = $dbConn->prepare("SELECT * FROM rooms ORDER BY RoomName ASC");
        $stmt->execute();
        return $stmt->fetchAll($dbConn::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

$addSuccess = null;
$addErrors = [];
$editSuccess = null;
$editErrors = [];

// Handle Add Schedule
if (isset($_POST['btnAdd'])) {
    $facultyID = trim($_POST['addFacultyID']);
    $curriculumID = trim($_POST['addCurriculumID']);
    $days = isset($_POST['addDays']) ? $_POST['addDays'] : [];
    $startTime = trim($_POST['addStartTime']);
    $endTime = trim($_POST['addEndTime']);
    $roomID = trim($_POST['addRoomID']);
    $sectionID = trim($_POST['addSectionID']);

    if (empty($facultyID) || empty($curriculumID) || empty($days) || empty($startTime) || empty($endTime) || empty($roomID) || empty($sectionID)) {
        $addSuccess = false;
        $addErrors[] = "All fields are required.";
    } else {
        $addSuccess = true;
        try {
            $conn->beginTransaction();
            $stmtInsert = $conn->prepare("INSERT INTO schedules (CurriculumID, FacultyID, Day, StartTime, EndTime, RoomID, SectionID) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($days as $day) {
                $stmtInsert->execute([$curriculumID, $facultyID, $day, $startTime, $endTime, $roomID, $sectionID]);
            }
            $conn->commit();
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">Schedule(s) added successfully!</span>
                </div>';
        } catch (PDOException $e) {
            $conn->rollBack();
            $addSuccess = false;
            $addErrors[] = "Error adding schedule(s): " . $e->getMessage();
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Failed to add schedule(s). ' . htmlspecialchars($e->getMessage()) . '</span>
                </div>';
        }
    }
}

// Handle Edit Schedule
if (isset($_POST['btnEdit'])) {
    $scheduleID = trim($_POST['editScheduleID']);
    $facultyID = trim($_POST['editFacultyID']);
    $curriculumID = trim($_POST['editCurriculumID']);
    $day = trim($_POST['editDay']);
    $startTime = trim($_POST['editStartTime']);
    $endTime = trim($_POST['editEndTime']);
    $roomID = trim($_POST['editRoomID']);
    $sectionID = trim($_POST['editSectionID']);

    if (empty($scheduleID) || empty($facultyID) || empty($curriculumID) || empty($day) || empty($startTime) || empty($endTime) || empty($roomID) || empty($sectionID)) {
        $editSuccess = false;
        $editErrors[] = "All fields are required.";
    } else {
        try {
            $stmtUpdate = $conn->prepare("UPDATE schedules SET CurriculumID = ?, FacultyID = ?, Day = ?, StartTime = ?, EndTime = ?, RoomID = ?, SectionName = ? WHERE ScheduleID = ?");
            if ($stmtUpdate->execute([$curriculumID, $facultyID, $day, $startTime, $endTime, $roomID, $sectionID, $scheduleID])) {
                $editSuccess = true;
                echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">Schedule updated successfully!</span>
                    </div>';
            } else {
                $editSuccess = false;
                $editErrors[] = "Failed to update schedule.";
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Failed to update schedule. Please try again.</span>
                    </div>';
            }
        } catch (PDOException $e) {
            $editSuccess = false;
            $editErrors[] = "Error updating schedule: " . $e->getMessage();
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Failed to update schedule. ' . htmlspecialchars($e->getMessage()) . '</span>
                </div>';
        }
    }
}

// Handle Delete Schedule
if (isset($_POST['deleteScheduleID'])) {
    $deleteScheduleID = $_POST['deleteScheduleID'];
    try {
        $stmtDelete = $conn->prepare("DELETE FROM schedules WHERE ScheduleID = ?");
        if ($stmtDelete->execute([$deleteScheduleID])) {
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">Schedule deleted successfully!</span>
                </div>';
        } else {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Failed to delete schedule. Please try again.</span>
                </div>';
        }
    } catch (PDOException $e) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Failed to delete schedule. ' . htmlspecialchars($e->getMessage()) . '</span>
            </div>';
    }
    exit;
}

// AJAX request for filtered data with pagination and search
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $facultyFilter = isset($_GET['faculty']) ? $_GET['faculty'] : '';
    $dayFilter = isset($_GET['day']) ? $_GET['day'] : '';
    $sectionFilter = isset($_GET['section']) ? $_GET['section'] : '';
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $rowsPerPageOptions = [5, 10, 20, 50, 100];
    $rowsPerPage = isset($_GET['rowsPerPage']) && in_array($_GET['rowsPerPage'], $rowsPerPageOptions) ? $_GET['rowsPerPage'] : 10;
    $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = max(0, ($currentPage - 1) * $rowsPerPage);

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
        $whereClauses[] = "s.SectionName = ?";
        $queryParams[] = $sectionFilter;
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

    $sql = "SELECT s.ScheduleID, c.SubjectName, CONCAT(u.FirstName, ' ', u.LastName) AS FacultyName, s.Day, s.StartTime, s.EndTime, r.RoomName, sec.SectionName
            FROM schedules s
            JOIN curriculums c ON s.CurriculumID = c.CurriculumID
            JOIN facultymembers fm ON s.FacultyID = fm.FacultyID
            JOIN users u ON fm.UserID = u.UserID
            JOIN rooms r ON s.RoomID = r.RoomID
            JOIN sections sec ON s.SectionID= sec.SectionID
            $whereString
            ORDER BY s.ScheduleID ASC
            LIMIT ?, ?";

    $stmt = $conn->prepare($sql);

    try {
        $paramIndex = 1;
        if (!empty($queryParams)) {
            foreach ($queryParams as $param) {
                $stmt->bindValue($paramIndex++, $param);
            }
        }
        $stmt->bindValue($paramIndex++, $offset, $conn::PARAM_INT);
        $stmt->bindValue($paramIndex++, $rowsPerPage, $conn::PARAM_INT);

        $stmt->execute();
        $data = $stmt->fetchAll($conn::FETCH_ASSOC);

        if (empty($data)) {
            echo json_encode(['html' => '<tr><td colspan="9" class="text-center">No schedules found.</td></tr>']);
            exit;
        }

        $html = '';
        foreach ($data as $schedule) {
            $html .= '<tr>';
            $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($schedule['ScheduleID']) . '</td>';
            $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($schedule['SubjectName']) . '</td>';
            $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($schedule['FacultyName']) . '</td>';
            $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($schedule['Day']) . '</td>';
            $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($schedule['StartTime']) . '</td>';
            $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($schedule['EndTime']) . '</td>';
            $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($schedule['RoomName']) . '</td>';
            $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($schedule['SectionName']) . '</td>';
            $html .= '<td class="px-4 py-2 whitespace-nowrap text-center space-x-2">';
            $html .= '<button class="text-blue-600 hover:text-blue-900 edit-btn" data-schedule="' . htmlspecialchars($schedule['ScheduleID']) . '" title="Edit" aria-label="Edit">';
            $html .= '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" title="Edit" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
            $html .= '<path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>';
            $html .= '</svg>';
            $html .= '</button>';
            $html .= '<button class="text-red-600 hover:text-red-900 delete-btn" data-schedule="' . htmlspecialchars($schedule['ScheduleID']) . '" title="Delete" aria-label="Delete">';
            $html .= '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" title="Delete" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
            $html .= '<path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>';
            $html .= '</svg>';
            $html .= '</button>';
            $html .= '</td>';
            $html .= '</tr>';
        }

        echo json_encode(['html' => $html]);
        exit;

    } catch (PDOException $e) {
        echo json_encode(['html' => '<tr><td colspan="9" class="text-center">Error executing query.</td></tr>']);
        exit;
    }
}

// Load Data for Filters and Page Load
$data = [];
$totalRows = 0;
$rowsPerPageOptions = [5, 10, 20, 50, 100];
$rowsPerPage = isset($_GET['rowsPerPage']) && in_array($_GET['rowsPerPage'], $rowsPerPageOptions) ? $_GET['rowsPerPage'] : 10;
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = max(0, ($currentPage - 1) * $rowsPerPage);
$facultyFilter = isset($_GET['faculty']) ? $_GET['faculty'] : '';
$dayFilter = isset($_GET['day']) ? $_GET['day'] : '';
$sectionFilter = isset($_GET['section']) ? $_GET['section'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

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
    $whereClauses[] = "s.SectionName = ?";
    $queryParams[] = $sectionFilter;
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

$sql = "SELECT COUNT(*) AS total
        FROM schedules s
        JOIN curriculums c ON s.CurriculumID = c.CurriculumID
        JOIN facultymembers fm ON s.FacultyID = fm.FacultyID
        JOIN users u ON fm.UserID = u.UserID
        JOIN rooms r ON s.RoomID = r.RoomID
        JOIN sections sec ON s.SectionID= sec.SectionID
        $whereString";

$stmtCount = $conn->prepare($sql);
$stmtCount->execute($queryParams);
$row = $stmtCount->fetch($conn::FETCH_ASSOC);
$totalRows = $row['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

if ($totalRows > 0) {
    $sqlData = "SELECT s.ScheduleID, c.SubjectName, CONCAT(u.FirstName, ' ', u.LastName) AS FacultyName, s.Day, s.StartTime, s.EndTime, r.RoomName, sec.SectionName
                FROM schedules s
                JOIN curriculums c ON s.CurriculumID = c.CurriculumID
                JOIN facultymembers fm ON s.FacultyID = fm.FacultyID
                JOIN users u ON fm.UserID = u.UserID
                JOIN rooms r ON s.RoomID = r.RoomID
                JOIN sections sec ON s.SectionID= sec.SectionID
                $whereString
                ORDER BY s.ScheduleID ASC
                LIMIT ?, ?";

    $stmtData = $conn->prepare($sqlData);
    $paramIndex = 1;
    if (!empty($queryParams)) {
        foreach ($queryParams as $param) {
            $stmtData->bindValue($paramIndex++, $param);
        }
    }
    $stmtData->bindValue($paramIndex++, $offset, $conn::PARAM_INT);
    $stmtData->bindValue($paramIndex++, $rowsPerPage, $conn::PARAM_INT);
    $stmtData->execute();
    $data = $stmtData->fetchAll($conn::FETCH_ASSOC);
} else {
    $data = [];
}

// Fetch faculties for filter and modal dro$connwn
$stmtFaculties = $conn->prepare("SELECT fm.FacultyID, CONCAT(u.FirstName, ' ', u.LastName) AS FacultyName, u.FirstName, u.LastName FROM facultymembers fm JOIN users u ON fm.UserID = u.UserID ORDER BY u.FirstName, u.LastName");
$stmtFaculties->execute();
$faculties = $stmtFaculties->fetchAll($conn::FETCH_ASSOC);

// Fetch sections for filter and modal dro$connwn
$stmtSections = $conn->prepare("SELECT SectionID, SectionName FROM sections ORDER BY SectionName");
$stmtSections->execute();
$sections = $stmtSections->fetchAll($conn::FETCH_ASSOC);

// Days array for filter
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

?>
