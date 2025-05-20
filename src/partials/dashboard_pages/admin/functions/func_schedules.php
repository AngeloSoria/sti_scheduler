<?php
ob_start();

require_once __DIR__ . '/../../../../../config/dbConnection.php';


$db = new Database();
$conn = $db->getConnection();

function getActiveSchoolYearSemesterID()
{
    try {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT ID FROM school_year_semesters WHERE IsActive = 1 LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['ID'] : null;
    } catch (PDOException $e) {
        return null;
    }
}

function getActiveSemesterValue()
{
    try {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT Semester FROM school_year_semesters WHERE IsActive = 1 LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['Semester'] : null;
    } catch (PDOException $e) {
        return null;
    }
}

function getPreferredSubjectsByFaculty($facultyId, $semesterValue = null)
{
    try {
        $db = new Database();
        $conn = $db->getConnection();
        if ($semesterValue !== null) {
            $stmt = $conn->prepare("SELECT c.CurriculumID, c.SubjectName FROM preferredsubjects p JOIN curriculums c ON p.CurriculumID = c.CurriculumID WHERE p.FacultyID = ? AND c.Semester = ? ORDER BY c.SubjectName ASC");
            $stmt->execute([$facultyId, $semesterValue]);
        } else {
            $stmt = $conn->prepare("SELECT c.CurriculumID, c.SubjectName FROM preferredsubjects p JOIN curriculums c ON p.CurriculumID = c.CurriculumID WHERE p.FacultyID = ? ORDER BY c.SubjectName ASC");
            $stmt->execute([$facultyId]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getRooms()
{
    try {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM rooms ORDER BY RoomName ASC");
        $stmt->execute();
        return $stmt->fetchAll($conn::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getFaculties()
{
    try {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT fm.FacultyID, u.FirstName, u.LastName, fm.ProgramID FROM facultymembers fm JOIN users u ON fm.UserID = u.UserID ORDER BY u.LastName ASC, u.FirstName ASC");
        $stmt->execute();
        return $stmt->fetchAll($conn::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getSections()
{
    try {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT SectionID, SectionName, ProgramID FROM sections ORDER BY SectionName ASC");
        $stmt->execute();
        return $stmt->fetchAll($conn::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// New function to check schedule conflicts
function isScheduleConflicting($roomID, $days, $startTime, $endTime, $facultyID = null, $excludeScheduleID = null, $sectionID = null)
{
    // If no days provided, there can't be a conflict
    if (empty($days)) {
        return false;
    }

    $db = new Database();
    $conn = $db->getConnection();

    $activeSchoolYearSemesterID = getActiveSchoolYearSemesterID();

    $conflicting_schedules_count = 0;

    // [Conflict #1] Duplicate schedule for the same room and time.
    $placeholders = implode(',', array_fill(0, count($days), '?'));
    $sql = "SELECT COUNT(*) FROM schedules WHERE RoomID = ? AND Day IN ($placeholders) AND (StartTime < ? AND EndTime > ?) AND SchoolYearSemesterID = ?";
    $params = array_merge([$roomID], $days, [$endTime, $startTime, $activeSchoolYearSemesterID]);
    if ($excludeScheduleID !== null) {
        $sql .= " AND ScheduleID != ?";
        $params[] = $excludeScheduleID;
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $conflicting_schedules_count = $stmt->fetchColumn();

    // [Conflict #2] Duplicate schedule for the same faculty and time.
    if ($facultyID !== null) {
        $placeholders = implode(',', array_fill(0, count($days), '?'));
        $sql = "SELECT COUNT(*) FROM schedules WHERE FacultyID = ? AND Day IN ($placeholders) AND (StartTime < ? AND EndTime > ?) AND SchoolYearSemesterID = ?";
        $params = array_merge([$facultyID], $days, [$endTime, $startTime, $activeSchoolYearSemesterID]);
        if ($excludeScheduleID !== null) {
            $sql .= " AND ScheduleID != ?";
            $params[] = $excludeScheduleID;
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $conflicting_schedules_count += $stmt->fetchColumn();
    }

    // [Conflict #3] Duplicate schedule for the same section and time regardless of room.
    if ($sectionID !== null) {
        $placeholders = implode(',', array_fill(0, count($days), '?'));
        $sql = "SELECT COUNT(*) FROM schedules WHERE SectionID = ? AND Day IN ($placeholders) AND (StartTime < ? AND EndTime > ?) AND SchoolYearSemesterID = ?";
        $params = array_merge([$sectionID], $days, [$endTime, $startTime, $activeSchoolYearSemesterID]);
        if ($excludeScheduleID !== null) {
            $sql .= " AND ScheduleID != ?";
            $params[] = $excludeScheduleID;
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $conflicting_schedules_count += $stmt->fetchColumn();
    }

    return $conflicting_schedules_count > 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['context']) {
        case 'isScheduleConflicting':
            $roomID = isset($_POST['roomID']) ? intval($_POST['roomID']) : 0;
            $days = isset($_POST['days']) ? $_POST['days'] : [];
            $startTime = isset($_POST['startTime']) ? trim($_POST['startTime']) : '';
            $endTime = isset($_POST['endTime']) ? trim($_POST['endTime']) : '';
            $facultyID = isset($_POST['addFacultyID']) ? trim($_POST['addFacultyID']) : '';
            $sectionID = isset($_POST['sectionID']) ? intval($_POST['sectionID']) : null;

            $conflict = isScheduleConflicting($roomID, $days, $startTime, $endTime, $facultyID, null, $sectionID);
            header('Content-Type: application/json');
            echo json_encode(['conflict' => $conflict]);
            exit();

        case 'addSchedule':
            // Validate and sanitize input
            $facultyID = isset($_POST['addFacultyID']) ? intval($_POST['addFacultyID']) : 0;
            $curriculumID = isset($_POST['addCurriculumID']) ? intval($_POST['addCurriculumID']) : 0;
            $days = isset($_POST['addDays']) ? $_POST['addDays'] : [];
            $roomID = isset($_POST['addRoomID']) ? intval($_POST['addRoomID']) : 0;
            $sectionID = isset($_POST['addSectionID']) ? intval($_POST['addSectionID']) : 0;
            $startTime = isset($_POST['addStartTime']) ? trim($_POST['addStartTime']) : '';
            $endTime = isset($_POST['addEndTime']) ? trim($_POST['addEndTime']) : '';
            $facultyID = isset($_POST['addFacultyID']) ? trim($_POST['addFacultyID']) : '';

            $activeSchoolYearSemesterID = getActiveSchoolYearSemesterID();

            if ($facultyID > 0 && $curriculumID > 0 && !empty($days) && $roomID > 0 && $sectionID > 0 && !empty($startTime) && !empty($endTime)) {
                // Check for conflicts for each day
                $hasConflict = false;
                foreach ($days as $day) {
                    if (isScheduleConflicting($roomID, [$day], $startTime, $endTime, $facultyID, null, $sectionID)) {
                        $hasConflict = true;
                        break;
                    }
                }

                if ($hasConflict) {
                    echo '<div id="alert-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
    Schedule conflict detected. Please choose a different time, room, or section.
  </div>
  <script>
    setTimeout(function() {
        var alertElem = document.getElementById("alert-message");
        if (alertElem) {
            alertElem.style.display = "none";
        }
    }, 5000);
  </script>';
                } else {
                    try {
                        $insertSql = "INSERT INTO schedules (FacultyID, CurriculumID, Day, RoomID, SectionID, StartTime, EndTime, SchoolYearSemesterID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmtInsert = $conn->prepare($insertSql);

                        foreach ($days as $day) {
                            $stmtInsert->execute([$facultyID, $curriculumID, $day, $roomID, $sectionID, $startTime, $endTime, $activeSchoolYearSemesterID]);
                        }

                        // Redirect to refresh
                        header("Location: dashboard?" . http_build_query($_GET));
                        exit();
                    } catch (PDOException $e) {
                        echo '<div id="alert-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        Error adding schedule.
                      </div>
                      <script>
                        setTimeout(function() {
                            var alertElem = document.getElementById("alert-message");
                            if (alertElem) {
                                alertElem.style.display = "none";
                            }
                        }, 5000);
                      </script>';
                    }
                }
            } else {
                echo '<div id="alert-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                Please fill in all required fields.
              </div>
              <script>
                setTimeout(function() {
                    var alertElem = document.getElementById("alert-message");
                    if (alertElem) {
                        alertElem.style.display = "none";
                    }
                }, 5000);
              </script>';
            }
            break;

        case 'editSchedule':
            // Validate and sanitize input
            $scheduleID = $_POST['editScheduleID'];
            $day = isset($_POST['editDay']) ? trim($_POST['editDay']) : '';
            $roomID = isset($_POST['editRoomID']) ? intval($_POST['editRoomID']) : 0;
            $sectionID = isset($_POST['editSectionID']) ? intval($_POST['editSectionID']) : 0;
            $startTime = isset($_POST['editStartTime']) ? trim($_POST['editStartTime']) : '';
            $endTime = isset($_POST['editEndTime']) ? trim($_POST['editEndTime']) : '';
            $facultyID = isset($_POST['editFacultyName']) ? trim($_POST['editFacultyName']) : '';

            $activeSchoolYearSemesterID = getActiveSchoolYearSemesterID();

            if ($scheduleID > 0 && !empty($day) && $roomID > 0 && $sectionID > 0 && !empty($startTime) && !empty($endTime)) {
                // Convert day string to array for conflict check
                $daysArray = [$day];
                // Check for schedule conflict excluding current scheduleID
                $conflict = isScheduleConflicting($roomID, $daysArray, $startTime, $endTime, $facultyID, $scheduleID, $sectionID);
                if ($conflict) {
                    echo '<div id="alert-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">Schedule conflict detected. Please choose a different time, room, or section.</div>
    <script>
        setTimeout(function() {
            var alertElem = document.getElementById("alert-message");
            if (alertElem) {
                alertElem.style.display = "none";
            }
        }, 5000);
    </script>';
                } else {
                    try {
                        $updateSql = "UPDATE schedules SET Day = ?, RoomID = ?, SectionID = ?, StartTime = ?, EndTime = ?, SchoolYearSemesterID = ? WHERE ScheduleID = ?";
                        $stmtUpdate = $conn->prepare($updateSql);
                        $stmtUpdate->execute([$day, $roomID, $sectionID, $startTime, $endTime, $activeSchoolYearSemesterID, $scheduleID]);

                        // Redirect to schedules.php with current query parameters to refresh the list
                        $queryParams = [];
                        if (isset($_GET['faculty'])) {
                            $queryParams['faculty'] = $_GET['faculty'];
                        }
                        if (isset($_GET['day'])) {
                            $queryParams['day'] = $_GET['day'];
                        }
                        if (isset($_GET['section'])) {
                            $queryParams['section'] = $_GET['section'];
                        }
                        if (isset($_GET['search'])) {
                            $queryParams['search'] = $_GET['search'];
                        }
                        if (isset($_GET['page'])) {
                            $queryParams['page'] = $_GET['page'];
                        }
                        if (isset($_GET['rowsPerPage'])) {
                            $queryParams['rowsPerPage'] = $_GET['rowsPerPage'];
                        }

                        $queryString = http_build_query($queryParams);
                    } catch (PDOException $e) {
                        // Handle error (optional: log error)
                        echo '<div id="alert-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">Error updating schedule.</div>
                        <script>
                            setTimeout(function() {
                                var alertElem = document.getElementById("alert-message");
                                if (alertElem) {
                                    alertElem.style.display = "none";
                                }
                            }, 5000);
                        </script>';
                    }
                }
            } else {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">Please fill in all required fields.</div>';
            }
            break;
        case 'deleteSchedule':
            // Validate and sanitize input
            $scheduleID = isset($_POST['deleteScheduleID']) ? intval($_POST['deleteScheduleID']) : 0;
            if (isset($scheduleID)) {
                try {
                    $deleteSql = "DELETE FROM schedules WHERE ScheduleID = ?";
                    $stmtDelete = $conn->prepare($deleteSql);
                    $stmtDelete->execute([$scheduleID]);
                } catch (PDOException $e) {
                    // Handle error (optional: log error)
                    echo '<div id="alert-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">Error deleting schedule.</div>
<script>
    setTimeout(function() {
        var alertElem = document.getElementById("alert-message");
        if (alertElem) {
            alertElem.style.display = "none";
        }
    }, 5000);
</script>';
                }
            } else {
                echo '<div id="alert-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">Invalid schedule ID.</div>
<script>
    setTimeout(function() {
        var alertElem = document.getElementById("alert-message");
        if (alertElem) {
            alertElem.style.display = "none";
        }
    }, 5000);
</script>';
            }
            break;
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
            $keywordClause[] = "LOWER(u.FirstName) LIKE LOWER(?)";
            $keywordClause[] = "LOWER(u.LastName) LIKE LOWER(?)";
            $keywordClause[] = "LOWER(r.RoomName) LIKE LOWER(?)";
            $keywordClause[] = "LOWER(se.SectionName) LIKE LOWER(?)";
            $keywordClauses[] = '(' . implode(' OR ', $keywordClause) . ')';
            $likeKeyword = '%' . $keyword . '%';
            $queryParams[] = $likeKeyword;
            $queryParams[] = $likeKeyword;
            $queryParams[] = $likeKeyword;
            $queryParams[] = $likeKeyword;
            $queryParams[] = $likeKeyword;
        }
        $whereClauses[] = '(' . implode(' AND ', $keywordClauses) . ')';
    }
}

$whereString = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Count total rows
$countSql = "SELECT COUNT(*) FROM schedules s
    JOIN curriculums c ON s.CurriculumID = c.CurriculumID
    JOIN facultymembers f ON s.FacultyID = f.FacultyID
    JOIN users u ON f.UserID = u.UserID
    JOIN rooms r ON s.RoomID = r.RoomID
    JOIN sections se ON s.SectionID = se.SectionID
    $whereString";

$stmtCount = $conn->prepare($countSql);
$stmtCount->execute($queryParams);
$totalRows = $stmtCount->fetchColumn();
$totalPages = ceil($totalRows / $rowsPerPage);

// Fetch data with joins
$sql = "SELECT s.ScheduleID, c.SubjectName, CONCAT(u.FirstName, ' ', u.LastName) AS FacultyName, s.Day, s.StartTime, s.EndTime, r.RoomName, se.SectionName, se.SectionID, r.RoomID
    FROM schedules s
    JOIN curriculums c ON s.CurriculumID = c.CurriculumID
    JOIN facultymembers f ON s.FacultyID = f.FacultyID
    JOIN users u ON f.UserID = u.UserID
    JOIN rooms r ON s.RoomID = r.RoomID
    JOIN sections se ON s.SectionID = se.SectionID
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
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $rowsPerPage, PDO::PARAM_INT);

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $data = [];
    $totalRows = 0;
    $totalPages = 0;
}